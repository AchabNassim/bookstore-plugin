<?php

// Object which stores all the table queries.
// Upon construction it creates the table if it doesn't exist already.
// *** The table is a self referencing table which is not the optimal way of doing things. \
//     it should work just fine, but the normal way of doing things would be to create two tables \
//     and create relations between them. time constraints ¯\_(ツ)_/¯.

class Db_category {
    private $db;
    private $table;

    // init props and create table
    public function __construct($wpdb, $table = 'wp_' . 'category') {
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
            category text NOT NULL UNIQUE,
            parent_id bigint(10) DEFAULT NULL,
            PRIMARY KEY  (id),
            CONSTRAINT fk_parent FOREIGN KEY (parent_id) REFERENCES $this->table(id) ON DELETE CASCADE
          ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    // fetch queries
    public function fetch_categories() {
        return ($this->db->get_results("SELECT * FROM {$this->table}"));
    }

    public function fetch_sub_categories($id) {
        return $this->db->get_results($this->db->prepare("SELECT * FROM {$this->table} WHERE `parent_id` = %d", $id));
    }

    public function category_exists($category) {
        return $this->db->get_results($this->db->prepare("SELECT * FROM {$this->table} WHERE `category` = %s", $category));
    }

    public function subcategory_exists($subcategory) {
        return $this->db->get_results($this->db->prepare("SELECT * FROM {$this->table} WHERE `category` = %s", $subcategory));
    }

    // crud queries
    public function insert_category($category) {
        return $this->db->insert($this->table, ['category' => $category], ['%s']);
    }

    public function insert_sub_category($category, $parent_id) {
        return $this->db->insert($this->table, ['category' => $category, 'parent_id' => $parent_id], ['%s', '%d']);
    }

    public function update_category($id, $new_category) {
        return $this->db->update(
            $this->table,
            ['category' => $new_category],
            ['id' => $id],
            ['%s'],
            ['%d']
            );
    }

    public function delete_category($id) {
        return $this->db->delete($this->table, ['id' => $id], ['%d']);
    }
}

?>