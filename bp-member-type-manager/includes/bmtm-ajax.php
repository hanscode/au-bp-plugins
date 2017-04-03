<?php

function bmtm_ajax_make_slug(){
    $name = sanitize_text_field( $_POST['name'] );
    die( bmtm_convert_name_to_slug($name) );
}
add_action('wp_ajax_bmtm_make_slug','bmtm_ajax_make_slug');


function bmtm_ajax_add_new_member_type(){

    check_admin_referer( 'bmtm_add_member_type' );

    $name = sanitize_text_field( $_POST['member-type-name'] );
    $plural_name = sanitize_text_field( $_POST['member-type-name-plural'] );
    $slug = sanitize_title( $_POST['member-type-slug'] );
    $directory = sanitize_text_field( $_POST['member-type-enable-directory'] );
    $is_default = sanitize_text_field( $_POST['member-type-make-default'] );


    if( empty($name) ){
        die("-1 Failed to create. Please put member type name properly.");
    }

    if( empty($slug) )
        $slug = bmtm_convert_name_to_slug( $name );

    $args = array(
        'name'                          => $name,
        'plural_name'                   => empty($plural_name) ? $name : $plural_name,
        'slug'                          => $slug,
        'has_directory'                 => intval($directory),
        'is_default'                    => empty($is_default) ? false : true
    );

    if( !bmtm_save_member_type( $slug, $args ) )
        die("-1 Failed! Something went wrong, Please try later.");
    else
        die("Member Type added!");

}
add_action('wp_ajax_bmtm_add_member_type','bmtm_ajax_add_new_member_type');




