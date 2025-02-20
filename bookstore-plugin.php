<?php

/**
* Plugin Name: Bookstore
* Description: This plugin allows you to create and manage books inside your wordpress website.
* Author: Nassim Achab
* Version: 1.0
* Author URI: https://nassimachab.me/
*/

if (!defined('ABSPATH')) {
    die();
}

// Responsible for creating the plugin tables
include plugin_dir_path(__FILE__) . "includes/hooks/init_tables.php";

// Responsible for adding the plugin with it menus to the sidebar
include plugin_dir_path(__FILE__) . "includes/hooks/add_menu_item.php";

// Responsible for adding the books shortcode
include plugin_dir_path(__FILE__) . "includes/hooks/add_shortcode.php";

?>
