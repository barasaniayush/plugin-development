<?php

function sie_handle_form_submission() {
    if (isset($_POST['sie_form_submitted'])) {
        global $wpdb;

        $income = floatval($_POST['income']);
        $expense = floatval($_POST['expense']);
        $date = sanitize_text_field($_POST['date']);
        $user_id = get_current_user_id();

        $table_name = $wpdb->prefix . 'sie_records';

        $wpdb->insert($table_name, [
            'income' => $income,
            'expense' => $expense,
            'date' => $date,
            'user_id' => $user_id,
        ]);

        echo '<div class="sie-message">Record saved successfully.</div>';
    }
}
