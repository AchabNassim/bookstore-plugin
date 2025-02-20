<?php

require_once dirname(__DIR__, 5) . '/wp-load.php';

// insert category
function handle_insert_category($Category, $category) {
    if (!isset($category) || empty($category)) {
        return (false);
    }
    $s_category = trim(strtolower(sanitize_text_field($category)));
    return ($Category->insert_category($s_category));
}

// insert subcategory
function handle_insert_subcategory($Category, $categoryId, $subcategory) {
    if ((!isset($categoryId) || empty($categoryId)) || (!isset($subcategory) || empty($subcategory))) {
        return (false);
    }
    $s_categoryId = intval($categoryId);
    $s_subcategory = trim(strtolower(sanitize_text_field($subcategory)));
    return ($Category->insert_sub_category($s_subcategory, $s_categoryId));
}

function handle_delete_category($Category, $categoryId) {
    if (!isset($categoryId) || empty($categoryId)) {
        return (false);
    }
    $s_categoryId = intval($categoryId);
    return ($Category->delete_category($s_categoryId));
}

function handle_update_category($Category, $categoryId, $category) {
    if ((!isset($categoryId) || empty($categoryId)) || (!isset($category) || empty($category))) {
        return (false);
    }
    $s_categoryId = intval($categoryId);
    $s_category = trim(strtolower(sanitize_text_field($category)));
    return ($Category->update_category($s_categoryId, $s_category));
}

// handle the post request
function handle_form_post_request() {
    global $wpdb;
    $Category = new Db_category($wpdb);
    $result = false;

    if (!isset($_GET['_wpnonce']) || wp_verify_nonce($_GET['_wpnonce'])) {
        if (isset($_POST['submit-category'])) {
            $result = handle_insert_category($Category, $_POST['category']);
        } else if (isset($_POST['submit-subcategory'])) {
            $result = handle_insert_subcategory($Category, $_POST['category_id'], $_POST['subcategory']);
        } else if (isset($_POST['update-category'])) {
            $result = handle_update_category($Category, $_POST['id'], $_POST['category']);
        } else if (isset($_POST['delete-category'])) {
            $result = handle_delete_category($Category, $_POST['id']);
        }
    }

    $redirect_url = add_query_arg(['query_status' => $result == true ? "success" : "failure"], admin_url("admin.php?page=bookstore-admin-sub-category"));
    wp_redirect($redirect_url);
}

handle_form_post_request();

?>