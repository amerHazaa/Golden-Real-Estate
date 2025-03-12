<?php
// ملف إدارة العقارات

class RealEstate {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function create_property($data) {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        $this->wpdb->insert($table_name, $data);
    }

    public function update_property($id, $data) {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        $this->wpdb->update($table_name, $data, array('ID' => $id));
    }

    public function delete_property($id) {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        $this->wpdb->delete($table_name, array('ID' => $id));
    }

    public function get_property($id) {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $id));
    }

    public function get_all_properties() {
        $table_name = $this->wpdb->prefix . 'gre_properties';
        return $this->wpdb->get_results("SELECT * FROM $table_name");
    }
}