<?php

// Object which stores all the table queries.
// Upon construction it creates the table if it doesn't exist already.
// *** The book table should store the categories and subcategories with their fk keys \
//     instead of raw text values. time constraints ¯\_(ツ)_/¯ \

class Db_book {
    private $db;
    private $table;

    // init props and create table
    public function __construct($wpdb, $table = 'wp_' . 'book') {
        $this->db = $wpdb;
        $this->table = $table;

        $this->create_table();
    }

    // table creation query
    private function create_table() {
        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $this->table  (
            id bigint(10) NOT NULL AUTO_INCREMENT,
            -- time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            title text NOT NULL,
            author text NOT NULL,
            `description` text NOT NULL,
            price int(10) NOT NULL,
            category text NOT NULL,
            subcategory text NULL,
            cover_url varchar(500) NULL,
            PRIMARY KEY  (id)
          ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    // fetch queries
    public function fetch_books() {
        return ($this->db->get_results("SELECT * FROM {$this->table}"));
    }

    // crud queries
    public function insert_book($title, $author, $description, $price, $category, $subcategory, $cover_url) {
        return $this->db->insert($this->table, 
            ['title' => $title,
            'author' => $author,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'subcategory' => $subcategory,
            'cover_url' => $cover_url
            ],
            ['%s',
             '%s',
             '%s',
             '%d',
             '%s',
             '%s',
             '%s',
            ]
    
        );
    }

    public function update_book($id, $title, $author, $description, $price, $category, $subcategory, $cover_url) {
        return $this->db->update(
            $this->table,
            ['title' => $title,
             'author' => $author,
             'description' => $description,
             'price' => $price,
             'category' => $category,
             'subcategory' => $subcategory,
             'cover_url' => $cover_url
            ],
            ['id' => $id],
            ['%s',
             '%s',
             '%s',
             '%d',
             '%s',
             '%s',
             '%s',
            ],
            ['%d']
            );
    }

    public function delete_book($id) {
        return $this->db->delete($this->table, ['id' => $id], ['%d']);
    }
}

?>