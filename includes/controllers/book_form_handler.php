<?php

require_once dirname(__DIR__, 5) . '/wp-load.php';

function upload_file($cover) {
    // upload file to wp media folder
    $uploaded_file = wp_handle_upload($cover, ['test_form' => false]);

    // if the upload was successful return the url to save it in the DB
    if ($uploaded_file && !isset($uploaded_file['error']))
        return ($uploaded_file['url']);
    else
        return ("");
}


// check if all values are set and not empty
function check_valid_data($title, $author, $description, $price, $category, $subcategory) {
    if (
        !isset($title) || empty($title) ||
        !isset($author) || empty($author) ||
        !isset($description) || empty($description) ||
        empty($price) ||
        !isset($category) || empty($category) ||
        !isset($subcategory) || empty($subcategory)
    ) {
        return false;
    }
    return true;
}

// check if category and subcategory exist in db before inserting them
// *** In normal cases there is no need for this function as insertion is made with a fk, and a query with \
//     an unexisting fk would just fail, but since I'm storing values as raw text, it is needed.
function check_exists($category, $subcategory) {
    global $wpdb;
    $Category = new Db_category($wpdb);

    $categoryExists = $Category->category_exists($category);
    $subcategoryExists = $Category->subcategory_exists($subcategory);
    if (!$categoryExists) {
        return (false);
    } else if ($subcategory && !$subcategoryExists) {
        return (false);
    }
    return (true);
}

// insert book
function handle_insert_book($Book, $title, $author, $description, $price, $category, $subcategory, $cover) {
    if (!check_valid_data($title, $author, $description, $price, $category, $subcategory))
        return (false);

    $s_title = trim(sanitize_text_field($title));
    $s_author = trim(sanitize_text_field($author));
    $s_description = trim(sanitize_textarea_field($description));
    $s_price = intval($price);
    $s_category = trim(sanitize_text_field($category));
    $s_subcategory = trim(sanitize_text_field($subcategory)) === "None" ? null : trim(sanitize_text_field($subcategory));
    
    if (!check_exists($s_category, $s_subcategory)) {
        return (false);
    }

    $upload_url = upload_file($cover);
    
    return $Book->insert_book($s_title, $s_author, $s_description, $s_price, $s_category, $s_subcategory, $upload_url);
}

function handle_delete_book($Book, $bookId) {
    if (!isset($bookId) || empty($bookId)) {
        return (false);
    }
    $s_bookId = intval($bookId);
    return ($Book->delete_book($s_bookId));
}

function handle_update_book($Book, $bookId, $title, $author, $description, $price, $category, $subcategory, $url, $cover) {
    if (!check_valid_data($title, $author, $description, $price, $category, $subcategory))
        return (false);

    $s_bookId = intval($bookId);
    $s_title = trim(sanitize_text_field($title));
    $s_author = trim(sanitize_text_field($author));
    $s_description = trim(sanitize_textarea_field($description));
    $s_price =  intval($price);
    $s_category = trim(sanitize_text_field($category));
    $s_subcategory = trim(sanitize_text_field($subcategory)) === "None" ? null : trim(sanitize_text_field($subcategory));

    if (!check_exists($s_category, $s_subcategory)) {
        return (false);
    }
    
    $cover_url = trim(sanitize_text_field($url));
    $upload_url = upload_file($cover);
    if (!empty($upload_url)) {
        $cover_url = $upload_url;
    }


    return $Book->update_book($s_bookId, $s_title, $s_author, $s_description, $s_price, $s_category, $s_subcategory, $cover_url);
}

// handle the post request
function handle_form_post_request() {
    global $wpdb;
    $Book = new Db_book($wpdb);
    $result = false;

    if (!isset($_GET['_wpnonce']) || wp_verify_nonce($_GET['_wpnonce'])) {
        if (isset($_POST['submit-book'])) {
            $result = handle_insert_book($Book, $_POST['title'], $_POST['author'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['subcategory'], $_FILES['cover']);
        } else if (isset($_POST['update-book'])) {
            $result = handle_update_book($Book, $_POST['id'], $_POST['title'], $_POST['author'], $_POST['description'], $_POST['price'], $_POST['category'], $_POST['subcategory'], $_POST['cover_url'], $_FILES['cover']);
        } else if (isset($_POST['delete-book'])) {
            $result = handle_delete_book($Book, $_POST['id']);
        }
    
    }
    $redirect_url = add_query_arg(['query_status' => $result == true ? "success" : "failure"], admin_url("admin.php?page=bookstore-admin-menu"));
    wp_redirect($redirect_url);
}

handle_form_post_request();

?>