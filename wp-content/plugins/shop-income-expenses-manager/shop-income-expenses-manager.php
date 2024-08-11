<?php
/*
 * Plugin Name:       Shop Income and Expenditure Manager
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Ayush Barasani
 * Author URI:        https://ayushbarasani.com.np/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       income-expenditure-manager
 */

ob_start();

// Security check to prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin paths
define('SIE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SIE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once SIE_PLUGIN_DIR . 'inc/install.php';
include_once SIE_PLUGIN_DIR . 'inc/shortcodes.php';
// include_once SIE_PLUGIN_DIR . 'inc/functions.php';

// Enqueue styles and scripts
function sie_enqueue_styles() {
    wp_enqueue_style('sie-styles', SIE_PLUGIN_URL . 'assets/style.css');
    wp_enqueue_script('sie-ajax', plugin_dir_url(__FILE__) . 'assets/js/sie-ajax.js', array('jquery'), null, true);
    wp_enqueue_script( 'sie-script', plugin_dir_url(__FILE__) . 'assets/js/custom.js', array('jquery'), null, true);
    wp_localize_script('sie-ajax', 'sieAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'sie_enqueue_styles');

// Register the activation hook to create the database tables
register_activation_hook(__FILE__, 'sie_manager_install');

// Register the uninstall hook to delete the database tables
register_uninstall_hook(__FILE__, 'sie_manager_uninstall');

ob_end_clean();