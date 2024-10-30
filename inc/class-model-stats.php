<?php
class ITRR_Stats
{
    /**
     * Tab table name
     */
    const table_name = 'itrr_stats';

    /** Create Table */
    public static function create_table()
    {
        global $wpdb;
        // create list table
        $creation_query =
            'CREATE TABLE IF NOT EXISTS ' . self::table_name . ' (
                `id` int(20) NOT NULL AUTO_INCREMENT,
                `trigger_id` int(255),
                `date` DATE NOT NULL ,
                `impression` int(255),
                `conversion` int(255),
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
     * Get stats by scenario_id
     * @param $id
     */
    public static function get_stats_trigger($id)
    {
        global $wpdb;
        $stats = array(
            'impression' => 0,
            'conversion' => 0,
            'rate' => '0.00%'
        );
        $query = 'select impression, conversion from ' . self::table_name . ' where trigger_id=' . $id . ';';
        $results = $wpdb->get_results($query, ARRAY_A);

        if(is_array($results) && count($results) > 0)
        {
            foreach($results as $result) {
                $stats['impression'] += $result['impression'];
                $stats['conversion'] += $result['conversion'];
            }
            $stats['rate'] = number_format(floatval($stats['conversion'] * 100 / $stats['impression']), 2) . " %";
        }
        return $stats;

    }

    /*
     * Get stats by date
     */
    public static function get_stats_period($id, $from, $to)
    {
        global $wpdb;
        $stats = array(
            'impression' => 0,
            'conversion' => 0,
            'rate' => '0.0'
        );
        $query = "SELECT impression, conversion FROM " . self::table_name . " WHERE trigger_id = " . $id . " AND date BETWEEN '" . $from . "' AND '" . $to . "';";
        $results = $wpdb->get_results($query, ARRAY_A);

        if(is_array($results) && count($results) > 0)
        {
            foreach($results as $result)
            {
                $stats['impression'] += $result['impression'];
                $stats['conversion'] += $result['conversion'];
            }
            $stats['rate'] = number_format(floatval($stats['conversion'] * 100 / $stats['impression']), 1);
        }

        return $stats;
    }

    /**
     * Update stats for trigger
     */
    public static function update_stats_trigger($id, $action = 'impression')
    {
        global $wpdb;
        $current_date = date('Y-m-d');
        if($action == 'impression')
        {

            $query = "UPDATE ".self::table_name. " SET impression = impression + 1 WHERE trigger_id =".$id." AND date = '".$current_date."';";
            $result = $wpdb ->query($query);

            if($result === false || $result == false){
                $query = 'INSERT INTO ' .  self::table_name  . ' ';
                $query .= '(trigger_id,date,impression,conversion) ';
                $query .= "VALUES ('{$id}','{$current_date}','1','0')";
                $wpdb->query( $query );
            }
        }
        elseif($action == 'conversion')
        {
            $query = "UPDATE ".self::table_name. " SET conversion = conversion + 1 WHERE trigger_id =".$id." AND date ='".$current_date."';";
            $wpdb ->query($query);
        }
        return true;
    }

    /** clear data */
    public static function clear_table()
    {
        global $wpdb;
        $wpdb ->query("TRUNCATE TABLE " . self::table_name);
        return true;
    }

}