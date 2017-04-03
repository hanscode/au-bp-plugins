<?php

/* plugin name: BP Member Type Manager
 * Plugin URI: http://rimonhabib.com
 * Description: Create and Manage member types of your BuddyPress site.
 * Author: Rimon Habib
 * Version: 1.01
 * Author URI:  http://rimonhabib.com,
 * License: GPLv2 or later
 */


class BP_Member_Type_Manager{

    private static $instance;

    public $options = array();

    function __construct(){

    }

    public static function getInstance() {
        if ( !self::$instance ) {
            self::$instance = new BP_Member_Type_Manager();

            self::$instance->define_constants();
            self::$instance->setup_globals();
            self::$instance->get_options();
            self::$instance->includes();
            self::$instance->hook_up();
        }

        return self::$instance;
    }


    function define_constants(){
        define('BMTM_DIR',                  basename(dirname(__FILE__))                                 );
        define('BMTM_INC_DIR',              'includes'                                                  );
        define('BMTM_ASSET_DIR',            'asset'                                                     );
        define('BMTM_TEMPLATE_DIR',         'templates'                                                 );
        define('BMTM_ADMIN_DIR',            'admin'                                                     );
        define('BMTM_ADMIN_SCREEN_DIR',     'screens'                                                   );

        define('BMTM_ROOT',                 trailingslashit(dirname(__FILE__))                          );
        define('BMTM_INC_PATH',             trailingslashit(BMTM_ROOT.BMTM_INC_DIR)                     );
        define('BMTM_ASSET_PATH',           trailingslashit(BMTM_ROOT.BMTM_ASSET_DIR)                   );
        define('BMTM_TEMPLATE_PATH',        trailingslashit(BMTM_ROOT.BMTM_TEMPLATE_DIR)                );
        define('BMTM_ADMIN_PATH',           trailingslashit(BMTM_ROOT.BMTM_ADMIN_DIR)                   );
        define('BMTM_ADMIN_SCREEN_PATH',    trailingslashit(BMTM_ADMIN_PATH.BMTM_ADMIN_SCREEN_DIR)      );

        define('BMTM_ROOT_URL',             trailingslashit( plugins_url(BMTM_DIR) )                    );
        define('BMTM_ASSET_URL',            trailingslashit( BMTM_ROOT_URL.BMTM_ASSET_DIR )             );
        define('BMTM_CSS_URL',              trailingslashit( BMTM_ASSET_URL.'css' )                     );
        define('BMTM_JS_URL',               trailingslashit( BMTM_ASSET_URL.'js' )                      );
        define('BMTM_IMAGE_URL',            trailingslashit( BMTM_ASSET_URL.'images' )                  );


        define('BMTM_ADMIN_MENU_SLUG',      'bmtm_manage_member_types'                                   );

    }


    function setup_globals(){

    }

    public function get_options(){

        if( empty($this->options) )
            $this->options = get_option('bmtm_options');

        if(empty($this->options['settings'])){
            $this->options['settings']['separate_registration'] = 1;
            $this->options['settings']['sync_user_role'] = 1;

        }

        return $this->options;
    }

    public function save_options( $options ){
        if( empty($options) )
            return false;

        $this->options = $options;
        return update_option('bmtm_options', $this->options);
    }

    function includes(){

        require_once( BMTM_INC_PATH.'bmtm-settings-api.class.php' );
        require_once( BMTM_INC_PATH.'bp-member-type-manager-functions.php' );
        require_once( BMTM_INC_PATH.'bp-member-type-manager-actions.php' );
        require_once( BMTM_INC_PATH.'bp-member-type-manager-hooks.php' );
        require_once( BMTM_INC_PATH.'bmtm-ajax.php' );

        if( is_admin() ){
            require_once( BMTM_ADMIN_PATH.'class-bp-member-types-list-table.php' );
            require_once( BMTM_ADMIN_PATH.'bmtm-admin.php' );

        }
    }


    function hook_up(){


        if(is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'load_css_js'));
        }
    }

    function load_css_js(){
        if(is_admin()) {

            wp_enqueue_script('bmtm-admin-script', BMTM_JS_URL . 'admin-script.js', array('jquery'));
        }

    }
}


function bp_member_type_manager(){
    global $bmtm;
    $bmtm = BP_Member_Type_Manager::getInstance();
    return $bmtm;
}
add_action('plugins_loaded','bp_member_type_manager');
