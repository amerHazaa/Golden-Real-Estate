<?php
// ملف إدارة الأبراج السكنية

class Tower {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function create_tower($data) {
        $table_name = $this->wpdb->prefix . 'gre_towers';
        $this->wpdb->insert($table_name, $data);
    }

    public function update_tower($id, $data) {
        $table_name = $this->wpdb->prefix . 'gre_towers';
        $this->wpdb->update($table_name, $data, array('ID' => $id));
    }

    public function delete_tower($id) {
        $table_name = $this->wpdb->prefix . 'gre_towers';
        $this->wpdb->delete($table_name, array('ID' => $id));
    }

    public function get_tower($id) {
        $table_name = $this->wpdb->prefix . 'gre_towers';
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $id));
    }

    public function get_all_towers() {
        $table_name = $this->wpdb->prefix . 'gre_towers';
        return $this->wpdb->get_results("SELECT * FROM $table_name");
    }
}