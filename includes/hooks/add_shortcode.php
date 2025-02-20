<?php

function books_grid_shortcode() {
    global $wpdb;
    $Books = new Db_book($wpdb);
    $results = $Books->fetch_books();

    if (!$results) {
        return '<p style="text-align: center;">No books found.</p>';
    }

    $output = '<div class="books-container">';

    foreach ($results as $book) {
        $image_url = !empty($book->cover_url) ? $book->cover_url : plugin_dir_url(__FILE__) . '../../assets/images/default.webp';

        $output .= '<div class="book-card">';
        $output .= '<img class="book-img" src="' . esc_url($image_url) . '" alt="' . esc_attr($book->title) . '">';
        $output .= '<div class="book-card-content">';
        $output .= '<h3>' . esc_html($book->title) . '</h3>';
        $output .= '<p><strong>Author:</strong> ' . esc_html($book->author) . '</p>';
        $output .= '<p><strong>Category:</strong> ' . esc_html($book->category) . '</p>';
        $output .= '<p><strong>Subcategory:</strong> ' . esc_html($book->subcategory) . '</p>';
        $output .= '<p><strong>Price:</strong> $' . esc_html($book->price) . '</p>';
        $output .= '<p class="description">' . esc_html($book->description) . '</p>';
        $output .= '</div></div>';
    }

    $output .= '</div>';

    return $output;
}

add_shortcode('books_grid', 'books_grid_shortcode');

function register_display_books_shortcode() {
    add_shortcode('display_books', 'books_grid_shortcode');
}

add_action('init', 'register_display_books_shortcode');

function enqueue_books_grid_styles() {
    wp_enqueue_style( 'my-theme',  plugin_dir_url(__FILE__) . "../../assets/css/books-shortcode.css", false);
};

add_action('wp_enqueue_scripts', 'enqueue_books_grid_styles');

?>