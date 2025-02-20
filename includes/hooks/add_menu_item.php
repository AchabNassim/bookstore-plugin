<?php

// Add menu items hook callback
function bookstore_admin_menu() {
    add_menu_page('Bookstore', 'Manage Books', 'manage_options', 'bookstore-admin-menu', 'bookstore_admin_menu_main', 'dashicons-book', 4);
    add_submenu_page('bookstore-admin-menu', 'categories', 'Manage categories', 'manage_options', 'bookstore-admin-sub-category', 'bookstore_admin_sub_category');
}

// callback to render menu page
function bookstore_admin_menu_main() {
    include WP_PLUGIN_DIR . "/book-store/templates/books-view.php";
}

// callback to render submenu page
function bookstore_admin_sub_category () {
    include WP_PLUGIN_DIR . "/book-store/templates/categories-view.php";
}

// load scripts for menu pages
function enqueue_admin_menu_scripts($hook) {
    if ($hook == 'manage-books_page_bookstore-admin-sub-category') {
        wp_enqueue_style( 'my-theme',  plugin_dir_url(__FILE__) . "../../assets/css/categories.css", false);
        wp_enqueue_script( 'my-script',  plugin_dir_url(__FILE__) . "../../assets/js/categories.js", [], null, true);
    } else if ($hook == 'toplevel_page_bookstore-admin-menu') {
        wp_enqueue_style( 'my-theme',  plugin_dir_url(__FILE__) . "../../assets/css/books.css", false);
        wp_enqueue_script( 'my-script',  plugin_dir_url(__FILE__) . "../../assets/js/book.js", [], null, true);
    }
}

// register actions
add_action('admin_enqueue_scripts', 'enqueue_admin_menu_scripts' );
add_action('admin_menu', 'bookstore_admin_menu');

?>