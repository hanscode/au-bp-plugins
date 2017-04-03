<?php

function bmtm_get_options(){
    global $bmtm;
    $options = $bmtm->get_options();
    return $options;
}

function bmtm_get_all_member_type( $args = array() ) {

    $cache_key = 'bmtm_member_types_all';
    $items     = wp_cache_get( $cache_key, 'bmtm' );

    if ( false === $items ) {

        $member_types = (array)bp_get_member_types(array(), 'objects');
        if( !count($member_types) )
            return array();

        foreach( $member_types as $index => $member_types_object ){
            $item = new stdClass();
            $item->name = $member_types_object->labels['name'];
            $item->slug = $index;
            $item->has_directory = $member_types_object->has_directory;

            $items[] =  $item;
        }

        wp_cache_set( $cache_key, $items, 'bmtm' );
    }


    return $items;
}

function bmtm_get_member_types_count() {

    return count(bmtm_get_all_member_type()) ;
}

function bmtm_save_member_type( $slug, $args = array(), $action = false ){
    global $bmtm;

    $options = $bmtm->options;

    if( empty( $options ) )
        $options = $bmtm->get_options();

    if( empty($slug) || !is_string($slug) )
        return false;

    if( empty($action) && bmtm_is_member_type_exist($slug) )
        return false;

    $defaults = array(
        'name'                          => '',
        'plural_name'                   => '',
        'slug'                          => $slug,
        'has_directory'                 => true,
        'is_default'                    => false,
        'user_role'                     => false,
        'settings'                      => array(
            'is_editable'               => false,
            'separate_register_screen'  => true,
        )
    );

    if( bmtm_is_member_type_exist($slug) && empty($action) ) {

        return false;
    }
    elseif( !bmtm_is_member_type_exist($slug) && empty($action)){
        $args = bp_parse_args( $args, $defaults );
    }
    elseif( bmtm_is_member_type_exist($slug) && !empty($action)){
        $args = bp_parse_args( $args, (array) $options['types'][$slug] );
    }




    if( empty($args['name']) )
        return false;

    if( empty($args['plural_name']) )
        $args['plural_name'] = $args['name'];



    do_action('bmtm_save_member_type', $args);
    $args = apply_filters( 'bmtm_before_save_member_type',$args );

    $options['types'][$slug] = $args;

    if( true === (bool)$args['is_default'] ){
        foreach( $options['types'] as $index =>  $type ){
            $options['types'][$index]['is_default'] = false;
        }

        $options['types'][$slug]['is_default'] = true;
    }

    if( ! in_array( $args['user_role'], wp_roles()->roles ) )
        $args['user_role'] = false;

    return $bmtm->save_options( $options );
}

function bmtm_get_member_type( $slug = '' ) {
    global $bmtm;

    if( empty($slug) )
        return false;

    $options = $bmtm->get_options();

    if( !empty($options['types'][$slug]) )
        return $options['types'][$slug];

    return false;
}

function bmtm_delete_member_type( $slug ){
    global $bmtm;
    if( empty($slug) )
        return false;

    $options = $bmtm->get_options();

    if( empty($options['types'][$slug]) )
        return false;

    unset($options['types'][$slug]);
    return $bmtm->save_options( $options );
}


function bmtm_convert_name_to_slug( $name ){
    return sanitize_title($name);
}

function bmtm_is_member_type_exist( $slug ){
    global $bmtm;

    if( empty($slug) )
        return false;

    $options = $bmtm->get_options();

    if( !empty($options['types'][$slug]) )
        return true;

    return false;
}

function bmtm_make_default_type( $slug ){
    global $bmtm;
    if(empty($slug))
        return false;

    $options = $bmtm->get_options();

    if( empty($options['types'][$slug]) )
        return false;

    foreach( $options['types'] as $index => $type ){
        $options['types'][$index]['is_default'] = false;
    }

    $options['types'][$slug]['is_default'] = true;

    return $bmtm->save_options();
}



function bmtm_update_user_role($user_id,$role) {
    if(!$user_id || !$role)
        return;
    $user = new WP_User( $user_id );
    $user->set_role($role);
}

function bmtm_get_user_role($user_id){
    if(!$user_id)
        return;
    $user = new WP_User( $user_id );
    return $user->roles[1];
}

function bmtm_update_member_type($user_id, $member_type ){
    if(!$user_id || !$member_type )
        return;

    return  bp_set_member_type($user_id, $member_type);
}