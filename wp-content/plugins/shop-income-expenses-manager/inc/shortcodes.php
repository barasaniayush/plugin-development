<?php

/**
 * Displays a form to input income and expenses, and displays a report of the data.
 *
 * @return string The HTML content to display the form and the report.
 */
function sie_display_income_expenses()
{
    ob_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $wpdb;

        if (isset($_POST['sie_submit'])) {
            // Handle adding income records
            $product_name = sanitize_text_field($_POST['product_name']);
            $amount = floatval($_POST['amount']);

            $table_name = $wpdb->prefix . 'sie_income';

            $wpdb->insert(
                $table_name,
                array(
                    'product_name' => $product_name,
                    'income_amount' => $amount,
                    'income_date' => current_time('mysql')
                ),
                array('%s', '%f', '%s')
            );
        } elseif (isset($_POST['sie_expense_submit'])) {
            // Handle adding expense records
            $description = sanitize_text_field($_POST['expense_description']);
            $amount = floatval($_POST['expense_amount']);
            $date = sanitize_text_field($_POST['expense_date']);

            $table_name = $wpdb->prefix . 'sie_expenses';

            $wpdb->insert(
                $table_name,
                array(
                    'description' => $description,
                    'expenses_amount' => $amount,
                    'expenses_date' => $date
                ),
                array('%s', '%f', '%s')
            );
        } elseif (isset($_POST['sie_edit_submit'])) {
            // Handle editing records
            $edit_type = sanitize_text_field($_POST['edit_type']);
            $table_name = $edit_type === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';

            $data = array(
                $edit_type === 'expense' ? 'expenses_amount' : 'income_amount' => floatval($_POST['edit_amount']),
                $edit_type === 'expense' ? 'expenses_date' : 'income_date' => sanitize_text_field($_POST['edit_date']),
            );

            if ($edit_type === 'expense') {
                $data['description'] = sanitize_text_field($_POST['edit_description']);
            } else {
                $data['product_name'] = sanitize_text_field($_POST['edit_product_name']);
            }

            $wpdb->update(
                $table_name,
                $data,
                array('id' => intval($_POST['edit_id'])),
                array('%f', '%s', '%s'),
                array('%d')
            );
        }
    }

    // Output the form and data display
?>
    <div class="sie-report">
        <h2>Income and Expenditure Report</h2>
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'addIncome')">Add Income</button>
            <button class="tablinks" onclick="openTab(event, 'addExpense')">Add Expense</button>
            <button class="tablinks" onclick="openTab(event, 'viewRecords')">View Records</button>
        </div>

        <div id="addIncome" class="tabcontent">
            <h3>Add Income</h3>
            <form method="post" action="">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="amount">Amount Collected:</label>
                <input type="number" id="amount" name="amount" step="0.01" required>

                <input type="submit" name="sie_submit" value="Submit">
            </form>
        </div>

        <div id="addExpense" class="tabcontent">
            <h3>Add Expense</h3>
            <form method="post" action="">
                <label for="expense_description">Expense Description:</label>
                <input type="text" id="expense_description" name="expense_description" required>

                <label for="expense_amount">Amount Spent:</label>
                <input type="number" id="expense_amount" name="expense_amount" step="0.01" required>

                <label for="expense_date">Date:</label>
                <input type="date" id="expense_date" name="expense_date" required>

                <input type="submit" name="sie_expense_submit" value="Submit">
            </form>
        </div>

        <div id="viewRecords" class="tabcontent">
            <h3>View Records</h3>
            <div id="incomeRecords">
                <h4>Income Records</h4>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'sie_income';
                $results = $wpdb->get_results("SELECT * FROM $table_name");

                if ($results) {
                    echo '<table>';
                    echo '<tr><th>Product Name</th><th>Amount</th><th>Date</th><th>Actions</th></tr>';
                    foreach ($results as $row) {
                        echo '<tr>';
                        echo '<td>' . esc_html($row->product_name) . '</td>';
                        echo '<td>' . esc_html($row->income_amount) . '</td>';
                        echo '<td>' . esc_html($row->income_date) . '</td>';
                        echo '<td>
                             <button class="edit-btn" data-type="income" data-id="' . esc_attr($row->id) . '">Edit</button>
                             <button class="delete-btn" data-type="income" data-id="' . esc_attr($row->id) . '">Delete</button>
                         </td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No income records found.</p>';
                }
                ?>
            </div>

            <div id="expenseRecords">
                <h4>Expense Records</h4>
                <?php
                $table_name = $wpdb->prefix . 'sie_expenses';
                $results = $wpdb->get_results("SELECT * FROM $table_name");

                if ($results) {
                    echo '<table>';
                    echo '<tr><th>Description</th><th>Amount</th><th>Date</th><th>Actions</th></tr>';
                    foreach ($results as $row) {
                        echo '<tr>';
                        echo '<td>' . esc_html($row->description) . '</td>';
                        echo '<td>' . esc_html($row->expenses_amount) . '</td>';
                        echo '<td>' . esc_html($row->expenses_date) . '</td>';
                        echo '<td>
                             <button class="edit-btn" data-type="expense" data-id="' . esc_attr($row->id) . '">Edit</button>
                             <button class="delete-btn" data-type="expense" data-id="' . esc_attr($row->id) . '">Delete</button>
                         </td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No expense records found.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="editForm" method="post" action="">
                <input type="hidden" id="edit_id" name="edit_id">
                <input type="hidden" id="edit_type" name="edit_type">
                <div id="editFields"></div>
                <input type="submit" name="sie_edit_submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Open the default tab
        document.getElementsByClassName('tablinks')[0].click();

        // Modal handling
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];
        var editBtns = document.getElementsByClassName("edit-btn");

        for (var i = 0; i < editBtns.length; i++) {
            editBtns[i].onclick = function() {
                var type = this.getAttribute('data-type');
                var id = this.getAttribute('data-id');
                var editFields = document.getElementById("editFields");

                jQuery.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    method: 'POST',
                    data: {
                        action: 'sie_get_record',
                        id: id,
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            var record = response.data;
                            document.getElementById("edit_id").value = record.id;
                            document.getElementById("edit_type").value = type;

                            if (type === 'expense') {
                                editFields.innerHTML = `
                                     <label for="edit_description">Expense Description:</label>
                                     <input type="text" id="edit_description" name="edit_description" value="${record.description}" required>
                                     
                                     <label for="edit_amount">Amount Spent:</label>
                                     <input type="number" id="edit_amount" name="edit_amount" step="0.01" value="${record.expenses_amount}" required>
                                     
                                     <label for="edit_date">Date:</label>
                                     <input type="date" id="edit_date" name="edit_date" value="${record.expenses_date}" required>
                                 `;
                            } else {
                                editFields.innerHTML = `
                                     <label for="edit_product_name">Product Name:</label>
                                     <input type="text" id="edit_product_name" name="edit_product_name" value="${record.product_name}" required>
                                     
                                     <label for="edit_amount">Amount Collected:</label>
                                     <input type="number" id="edit_amount" name="edit_amount" step="0.01" value="${record.income_amount}" required>
                                     
                                     <label for="edit_date">Date:</label>
                                     <input type="date" id="edit_date" name="edit_date" value="${record.income_date}" required>
                                 `;
                            }

                            modal.style.display = "block";
                        } else {
                            alert(response.data);
                        }
                    }
                });
            }
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php
    return ob_get_clean(); // Return the buffered content
}

add_shortcode('sie_report', 'sie_display_income_expenses');
?>

<?php
function sie_get_record()
{
    global $wpdb;
    $table_name = $_POST['type'] === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';
    $id = intval($_POST['id']);
    $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if ($record) {
        // Ensure dates are in the correct format
        $record->income_date = date('Y-m-d', strtotime($record->income_date));
        $record->expenses_date = date('Y-m-d', strtotime($record->expenses_date));

        wp_send_json_success($record);
    } else {
        wp_send_json_error('Record not found');
    }
}

add_action('wp_ajax_sie_get_record', 'sie_get_record');

function sie_edit_record()
{
    global $wpdb;

    $id = intval($_POST['edit_id']);
    $type = sanitize_text_field($_POST['edit_type']);

    $table_name = $type === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';

    $data = array(
        $type === 'expense' ? 'expenses_amount' : 'income_amount' => floatval($_POST['edit_amount']),
        $type === 'expense' ? 'expenses_date' : 'income_date' => sanitize_text_field($_POST['edit_date']),
    );

    if ($type === 'expense') {
        $data['description'] = sanitize_text_field($_POST['edit_description']);
    } else {
        $data['product_name'] = sanitize_text_field($_POST['edit_product_name']);
    }

    $updated = $wpdb->update(
        $table_name,
        $data,
        array('id' => $id),
        array('%f', '%s', '%s'),
        array('%d')
    );

    if ($updated !== false) {
        $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        wp_send_json_success($record);
    } else {
        wp_send_json_error('Failed to update the record.');
    }

    wp_die(); // This is required to terminate immediately and return a proper response
}

add_action('wp_ajax_sie_edit_record', 'sie_edit_record');


function sie_delete_record()
{
    global $wpdb;

    $id = intval($_POST['id']);
    $type = sanitize_text_field($_POST['type']);

    if ($type === 'expense') {
        $table_name = $wpdb->prefix . 'sie_expenses';
    } else {
        $table_name = $wpdb->prefix . 'sie_income';
    }

    $deleted = $wpdb->delete($table_name, array('id' => $id), array('%d'));

    if ($deleted) {
        wp_send_json_success('Record deleted successfully.');
    } else {
        wp_send_json_error('Failed to delete the record.');
    }

    wp_die(); // This is required to terminate immediately and return a proper response
}

add_action('wp_ajax_sie_delete_record', 'sie_delete_record');
add_action('wp_ajax_nopriv_sie_delete_record', 'sie_delete_record');

function sie_get_all_records()
{
    global $wpdb;
    
    $income_table = $wpdb->prefix . 'sie_income';
    $expense_table = $wpdb->prefix . 'sie_expenses';

    $income_records = $wpdb->get_results("SELECT * FROM $income_table");
    $expense_records = $wpdb->get_results("SELECT * FROM $expense_table");

    if ($income_records !== false && $expense_records !== false) {
        wp_send_json_success(array(
            'income' => $income_records,
            'expenses' => $expense_records
        ));
    } else {
        wp_send_json_error('Failed to retrieve records.');
    }

    wp_die();
}

add_action('wp_ajax_sie_get_all_records', 'sie_get_all_records');
