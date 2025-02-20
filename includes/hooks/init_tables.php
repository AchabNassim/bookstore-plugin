<?php

include plugin_dir_path(__FILE__) . "../instances/Category.php";
include plugin_dir_path(__FILE__) . "../instances/Book.php";

// Create database tables
function init_database() {
    global $wpdb;
    $Category = new Db_category($wpdb);
    $Book = new Db_book($wpdb);
}

add_action('init', 'init_database');

?>