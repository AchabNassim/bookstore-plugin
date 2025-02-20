<?php

include WP_PLUGIN_DIR . "/book-store/includes/instances/Category.php";
include WP_PLUGIN_DIR . "/book-store/includes/instances/Book.php";

// Create database tables
function init_database() {
    global $wpdb;
    $Category = new Db_category($wpdb);
    $Book = new Db_book($wpdb);
}

add_action('init', 'init_database');

?>