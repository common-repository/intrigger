<?php
/**
 * contact list class
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ITRR_Contacts_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct(
            array('singular' => __( 'Contact', 'itrr_lang' ), //singular name of the listed records
            'plural'   => __( 'Contacts', 'itrr_lang' ), //plural name of the listed records
            'ajax'     => false) //does this table support ajax?
        );

    }


    /**
     * Retrieve contacts data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_contacts( $per_page = 5, $page_number = 1 ) {

        $result = ITRR_Contacts::get_contacts();
        $start = ( $page_number - 1 ) * $per_page;
        
        usort( $result, array(__CLASS__, 'usort_reorder' ) );
		$result = array_slice($result, $start, $per_page);
        return $result;
    }

    /**
     * Delete a contact record.
     *
     * @param int $id customer ID
     */
    public static function delete_contact( $id ) {
        $result = ITRR_Contacts::removeContact($id);
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        $result = ITRR_Contacts::get_contacts();
        return count($result);
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No contacts avaliable.', 'itrr_lang' );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'email':
            case 'scenario':
            case 'indget':
            case 'page':
            case 'date':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        $delete_nonce = wp_create_nonce( 'itrr_delete_contact' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = array(
            'delete' => sprintf( '<a href="?page=%s&action=%s&contact=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'      => '<input type="checkbox" />',
            'email'    => __( 'Email', 'itrr_lang' ),
            'scenario' => __( 'Scenario', 'itrr_lang' ),
            'indget'    => __( 'Indget', 'itrr_lang' ),
            'page'    => __( 'Subscription page', 'itrr_lang' ),
            'date'    => __( 'Date', 'itrr_lang' )
        );

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'date' => array( 'date', true ),
            'email' => array( 'email', false ),
            'scenario' => array( 'scenario', false ),
            'indget' => array( 'indget', false ),
            'scenario' => array( 'scenario', false ),
            'page' => array( 'page', false )
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete'
        );

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'contacts_per_page', 50 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_contacts( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'itrr_delete_contact' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_contact( absint( $_GET['contact'] ) );

                wp_redirect(esc_url(add_query_arg(NULL,NULL))); exit;
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_contact( $id );
            }
            wp_redirect(esc_url(add_query_arg(NULL,NULL))); exit;

        }
    }
    public function pagination($which){
        echo '<a href="'.add_query_arg(array('page' => 'csv-download','csv_export'=>1), admin_url('admin.php')).'" id="export_contacts" class="itrr_button" style="float:right; margin: 2px 1px 8px 15px;" >'.__("Export contacts", "itrr_lang").'</a>';
        parent::pagination($which);
    }

    public static function csv_download(){

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=contacts.csv');

        $fp = fopen('php://output', 'w');
        $data = ITRR_Contacts::get_contacts();
        usort( $data, array( __CLASS__, 'usort_reorder' ) );
        foreach ($data as $line) {
            foreach($line as $key=>$column){
                $line[$key] = str_replace('&#8211;','-',$line[$key]);
            }
            fputcsv($fp, $line);
        }
        fclose($fp);

        die();
    }

    static function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date'; //email
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc'; //ask
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
}