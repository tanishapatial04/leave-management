<?php
// Create custom table for leave requests on activation
function elm_create_leave_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_leaves_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        leave_date VARCHAR(50) DEFAULT '',
        reason TEXT,
        status VARCHAR(20) DEFAULT 'Pending',
        leave_type VARCHAR(20) DEFAULT 'single',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}