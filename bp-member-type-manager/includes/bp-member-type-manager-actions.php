<?php

function bmtm_register_member_types(){
    global $bmtm;

    do_action('bmtm_register_member_types');

    $options = $bmtm->get_options();


    if( empty($options['types']) )
        return;


    foreach( $options['types'] as $slug => $type ){


        bp_register_member_type( $slug, array(
            'labels' => array(
                'name' => $type['plural_name'],
                'singular_name' => $type['name'],
            ),
            'has_directory' =>  !empty( $type['has_directory'] ) ? $slug : false
        ));
    }
}

add_action( 'bp_register_member_types', 'bmtm_register_member_types' );


function bmtm_sync_member_type_on_role_update( $user_id, $role, $old_role ){

    $user_synced = get_user_meta($user_id, 'bmtm_is_user_synched');
    if( !empty($user_synced) || $user_synced != 0 )
        return;

    $options = bmtm_get_options();

}
//add_action( 'set_user_role', 'bmtm_sync_member_type_on_role_update',10,3 );


function bmtm_sync_role_on_member_type_change( $user_id, $member_type, $append ){
    if( empty($user_id) || empty($member_type) )
        return false;

    $options = bmtm_get_options();

    if( empty($options['settings']['sync_user_role']) )
        return;

    if( empty( $options['types'][$member_type] ) )
        return false;
    $role = $options['types'][$member_type]['user_role'];
    return bmtm_update_user_role($user_id,$role);
}
add_action( 'bp_set_member_type', 'bmtm_sync_role_on_member_type_change',10,3 );


function bmtm_set_fake_page_for_registration($pages){


    $options = bmtm_get_options();

    if( empty($options['settings']['separate_registration']) )
        return;


    $current_slug = $pages->register->slug;


    $pages->register->slug = 'register/student';

    return $pages;
}
//add_filter('bp_core_get_directory_pages','bmtm_set_fake_page_for_registration');


function bmtm_path_fix_for_registration($path){

    global $bp;

    global $bmtm;
    $options = $bmtm->get_options();

    if( empty($options['settings']['separate_registration']) )
        return;


    $chunk = explode('/',trim($path,'/'));

    $base_registration_slug = $chunk[0];
    $requested_member_type = $chunk[1];

    $type_found = false;
    foreach( (array)$options['types'] as $type ){
        $default = !empty($type['is_default']) ? $type['slug'] : false;
        if( $type['slug'] === $requested_member_type ){
            $type_found = $type['slug'];
            $bmtm->requested_type = $type_found;
            break;
        }
    }

    if( empty($default) ){
        $default_type = array_shift( $options['types'] );
        $default = $default_type['slug'];
    }


    if( $bp->pages->register->slug === trim($path,'/') )
        bp_core_redirect( site_url($base_registration_slug.'/'.$default) );



    if( empty($type_found) )
        return $path;



    return trailingslashit($base_registration_slug);
}
add_filter('bp_uri','bmtm_path_fix_for_registration');


function bmtm_separate_registration_fields($args){

    global $bmtm,$bp;

    if( $bp->current_component !== 'register' || empty($bmtm->requested_type) )
        return $args;


    $args['member_type'] = $bmtm->requested_type;

    return $args;
}
add_filter( 'bp_before_has_profile_parse_args','bmtm_separate_registration_fields');


function bmtm_assign_member_type_on_registration( $user_id ){
    global $bmtm;

    if( empty($bmtm->requested_type) )
        return;

    bmtm_update_member_type( $user_id, $bmtm->requested_type );
    update_user_meta( $user_id, 'member_type', $bmtm->requested_type  );

}
add_action('bp_core_signup_user','bmtm_assign_member_type_on_registration',10,1);



function bmtm_assign_role_on_registration_activation( $user_id, $key, $user ){
    global $bmtm;
    $options = $bmtm->get_options();

    if( empty($options['settings']['sync_user_role']) )
        return;

    $member_type = get_user_meta( $user_id, 'member_type', true );
    $member = get_userdata( $user_id );
    $role = $options['types'][$member_type]['user_role'];
    $member->set_role( $role );
}
add_action('bp_core_activated_user','bmtm_assign_role_on_registration_activation', 10, 3);


function bmtm_admin_form_handler(){
    global $bmtm;
    $options = $bmtm->get_options();
    $current_page = sanitize_title( $_REQUEST['page'] );
    $action = sanitize_title( $_GET['action'] );
    $id = sanitize_title( $_GET['id'] );


    if ($current_page === BMTM_ADMIN_MENU_SLUG) {

        if ( sanitize_title( $_POST['bmtm_action']) === 'bmtm_add_member_type' || sanitize_title( $_POST['bmtm_action'] ) === 'bmtm_edit_member_type') {


            $member_type = array(
                'name' => !empty($_POST['member-type-name']) ? sanitize_text_field ( trim($_POST['member-type-name']) ) : false,
                'plural_name' => !empty($_POST['member-type-name-plural']) ? sanitize_text_field ( trim($_POST['member-type-name-plural']) ) : false,
                'slug' => !empty($_POST['member-type-slug']) ? sanitize_title( $_POST['member-type-slug'] ) : false,
                'has_directory' => !empty($_POST['member-type-enable-directory']) ? true : false,
                'is_default' => !empty($_POST['member-type-make-default']) ? true : false,
                'user_role' => !empty($_POST['member-type-user-role']) ?  sanitize_text_field ( $_POST['member-type-user-role'] )  : false,
            );

            $action = false;

            if( sanitize_title ( $_POST['bmtm_action'] ) === 'bmtm_edit_member_type' )
            $action = 'update';

            $is_saved = bmtm_save_member_type($member_type['slug'], $member_type, $action);
            if ($is_saved) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>Member Type is saved.</p></div>';
                });
            } else {
                add_action('admin_notices', function () {
                    echo '<div class="notice error is-dismissible"><p>failed! Please fill up all required filed and try again.</p></div>';
                });
            }

        }


        if ( sanitize_title( $_REQUEST['action'] ) === 'delete') {
            if (empty($id))
                wp_redirect(admin_url('admin.php?page=bmtm_manage_member_types'));


            if (bmtm_delete_member_type($id)) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-success is-dismissible"><p>Member Type is deleted.</p></div>';
                });
                wp_redirect(admin_url('admin.php?page=bmtm_manage_member_types'));
            } else
                add_action('admin_notices', function () {
                    echo '<div class="notice error is-dismissible"><p>failed to delete! try again later.</p></div>';
                });

        }

    }elseif( $current_page === 'bmtm_manage_member_types_settings' ){

        if( !empty($_POST['bmtm_action']) && sanitize_title( $_POST['bmtm_action'] ) === 'bmtm_save_member_type_settings' ){


        $separate_registration = !empty($_POST['member-type-separate-registration-page']) ? 1 : 0;
        $sync_user_role = !empty($_POST['member-type-user-role-sync']) ? 1 : 0;
        $enable_directory_tab = !empty($_POST['member-type-enable-directory-tab']) ? 1 : 0;

        $options['settings']['separate_registration'] = $separate_registration;
        $options['settings']['sync_user_role'] = $sync_user_role;
        $options['settings']['enable-directory-tab'] = $enable_directory_tab;
        $bmtm->save_options( $options );
        }
    }

}
add_action('bmtm_register_member_types','bmtm_admin_form_handler');

