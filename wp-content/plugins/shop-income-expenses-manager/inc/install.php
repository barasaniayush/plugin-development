<?php
/**
 * Install the database tables for the plugin.
 *
 * @return void
 */
function sie_manager_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create table for income records
    $income_table = "
        CREATE TABLE {$wpdb->prefix}sie_income (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_name varchar(255) NOT NULL,
            income_amount float(10, 2) NOT NULL,
            income_date date NOT NULL,
            user_id mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    
    // Create table for expense records
    $expense_table = "
        CREATE TABLE {$wpdb->prefix}sie_expenses (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            description text NOT NULL,
            expenses_amount float(10, 2) NOT NULL,
            expenses_date date NOT NULL,
            user_id mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($income_table);
    dbDelta($expense_table);
}

function sie_manager_uninstall() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sie_income");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sie_expenses");
}