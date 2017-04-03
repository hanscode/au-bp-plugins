<?php

if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class BP_Member_Type_List_Table extends \WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular' => 'Member Type',
            'plural'   => 'Member Types',
            'ajax'     => true
        ) );
    }

    function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
    }

    function no_items() {
        _e( 'No Member Type Found', 'bmtm' );
    }

    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'member_type_name':
                return $item->name;

            case 'has_directory':
                return $item->has_directory ? 'Enabled' : 'Desabled';

            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'member_type_name'      => __( 'Member Type Name', 'bmtm' ),
            'has_directory'         => __( 'Directory', 'bmtm' ),

        );

        return $columns;
    }

    function column_member_type_name( $item ) {

        $actions           = array();
        $actions['edit']   = sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page='.BMTM_ADMIN_MENU_SLUG.'&action=edit&id=' . $item->slug ), $item->id, __( 'Edit', 'bmtm' ), __( 'Edit', 'bmtm' ) );
        $actions['delete']      = sprintf( '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page='.BMTM_ADMIN_MENU_SLUG.'&action=delete&id=' . $item->slug ), $item->name, __( 'Delete', 'bmtm' ), __( 'Delete', 'bmtm' ) );

        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url( 'admin.php?page='.BMTM_ADMIN_MENU_SLUG.'&action=edit&id=' . $item->slug ), $item->name, $this->row_actions( $actions ) );
    }


    function get_bulk_actions() {
        $actions = array(
            //'trash'  => __( 'Move to Trash', 'bmtm' ),
        );
        return $actions;
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="member_type_id[]" value="%d" />', $item->slug
        );
    }

    public function get_views_() {
        $status_links   = array();
        $base_link      = admin_url( 'admin.php?page=sample-page' );

        foreach ($this->counts as $key => $value) {
            $class = ( $key == $this->page_status ) ? 'current' : 'status-' . $key;
            $status_links[ $key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => $key ), $base_link ), $class, $value['label'], $value['count'] );
        }

        return $status_links;
    }

    function prepare_items() {

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = array();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = 10;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';


        $this->items  = bmtm_get_all_member_type();

            $this->set_pagination_args( array(
                'total_items' => bmtm_get_member_types_count(),
                'per_page'    => $per_page
            ) );
        }
    }