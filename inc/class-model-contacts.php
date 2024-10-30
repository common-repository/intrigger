<?php
class ITRR_Contacts
{
    /**
     * Tab table name
     */
    const table_name = 'itrr_contacts';

    /** Create Table */
    public static function create_table()
    {
        global $wpdb;
        // create list table
        $creation_query =
            'CREATE TABLE IF NOT EXISTS ' . self::table_name . ' (
                `id` int(20) NOT NULL AUTO_INCREMENT,
                `email` varchar(30),
                `scenario` varchar(30),
                `indget` varchar(30),
                `page` varchar(30),
                `date` DATE NOT NULL,
                PRIMARY KEY (`id`)
                );';
        $wpdb->query( $creation_query );
    }

    /**
     * Remove table
     */
    public static function remove_table()
    {
        global $wpdb;
        $query = 'DROP TABLE IF EXISTS ' . self::table_name . ';';
        $wpdb->query($query);
    }
    /**
     * Get all contacts
     * @param $id
     */
    public static function get_contacts()
    {
        global $wpdb;

        $query = 'select * from ' . self::table_name . ';';
        $results = $wpdb->get_results($query, ARRAY_A);

        if(is_array($results) && count($results) > 0)
        {
            return $results;
        }
        return array();

    }

    /**
     * Add new contact
     */
    public static function addContact($data)
    {
        global $wpdb;
        $current_date = date('Y-m-d');

        $contact_deduped = get_option('itrr_setting_contacts');
        /* check if contact email already exists for dedupe setting */
        $check_query = "SELECT * FROM " .self::table_name ." WHERE email='".$data["email"]."';";
        $check_exist = $wpdb->get_results($check_query);
        if( $check_exist && $contact_deduped) {
            // update
            $query = 'UPDATE ' . self::table_name . ' ';
            $query .= "SET scenario='{$data['scenario']}',indget='{$data['indget']}',page='{$data['page']}',date='{$current_date}' ";
            $query .= "WHERE email='" . $data['email'] . "' ORDER BY id DESC LIMIT 1;";
            $wpdb->query($query);
        }
        else{
            // insert
            $query = 'INSERT INTO ' .  self::table_name  . ' ';
            $query .= '(email,scenario,indget,page,date) ';
            $query .= "VALUES ('{$data['email']}','{$data['scenario']}','{$data['indget']}','{$data['page']}','{$current_date}')";
            $wpdb->query( $query );
        }
        return true;
    }
    /**
     * remove a contact
     */
    public static function removeContact($id)
    {
        global $wpdb;

        $wpdb->delete( self::table_name,
            array('id' => $id)
        );
    }

    /** clear data */
    public static function clear_table()
    {
        global $wpdb;
        $wpdb ->query("TRUNCATE TABLE " . self::table_name);
        return true;
    }

}