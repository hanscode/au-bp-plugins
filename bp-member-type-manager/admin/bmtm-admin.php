<?php

function bmtm_register_admin_menu(){
    add_menu_page(
        'BP Member Types Manager',
        'BP Member Types',
        'administrator',
        BMTM_ADMIN_MENU_SLUG,
        'bmtm_admin_screen',
        'dashicons-groups'
    );

    add_submenu_page(
        BMTM_ADMIN_MENU_SLUG,
        'Manage Member Types',
        'Manage',
        'administrator',
        BMTM_ADMIN_MENU_SLUG,
        'bmtm_admin_screen'
    );



    add_submenu_page(
        BMTM_ADMIN_MENU_SLUG,
        'Member Types Page Settings',
        'Settings',
        'administrator',
        BMTM_ADMIN_MENU_SLUG.'_settings',
        'bmtm_admin_screen'
    );
}

add_action( 'admin_menu', 'bmtm_register_admin_menu' );

function bmtm_admin_screen(){
    $current_page = $_REQUEST['page'];
    $action = $_GET['action'];
    $id = $_GET['id'];

    $screen = str_replace(BMTM_ADMIN_MENU_SLUG,'',$current_page);
    if( empty($screen) ){
        $screen =  'member-types-list';
        if( $action === 'edit' && !empty($id) ){
            $screen = 'edit';
        }
    }


    do_action('bmtm_before_loading_admin_screen');
    require_once( BMTM_ADMIN_SCREEN_PATH. $screen.'.php' );
    do_action('bmtm_after_loading_admin_screen');

}


