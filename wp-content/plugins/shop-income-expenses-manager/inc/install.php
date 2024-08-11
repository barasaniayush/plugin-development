<?php

function sie_manager_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    $tables = [
        'sie_records' => "
            CREATE TABLE {$wpdb->prefix}sie_records (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                date date NOT NULL,
                income float(10, 2) DEFAULT 0 NOT NULL,
                expense float(10, 2) DEFAULT 0 NOT NULL,
                user_id mediumint(9) NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;",
        
        'sie_income_expenses' => "
            CREATE TABLE {$wpdb->prefix}sie_income_expenses (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                product_name varchar(255) NOT NULL,
                amount float NOT NULL,
                date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;"
    ];

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    foreach ($tables as $table_name => $sql) {
        dbDelta($sql);
    }
}

register_activation_hook(__FILE__, 'sie_manager_install');
?>