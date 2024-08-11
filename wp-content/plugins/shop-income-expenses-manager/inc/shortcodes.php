<?php

/**
 * Displays a form to input income and expenses, and displays a report of the data.
 *
 * @return string The HTML content to display the form and the report.
 */
function sie_display_income_expenses()
{
    ob_start(); // Start output buffering

    // Form handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['sie_submit'])) {
            // Handle adding income records
            $product_name = sanitize_text_field($_POST['product_name']);
            $amount = floatval($_POST['amount']);

            global $wpdb;
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

            global $wpdb;
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
            global $wpdb;
            $table_name = $_POST['edit_type'] === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';

            $data = array(
                'amount' => floatval($_POST['edit_amount']),
                'date' => sanitize_text_field($_POST['edit_date'])
            );

            if ($_POST['edit_type'] === 'expense') {
                $data['description'] = sanitize_text_field($_POST['edit_description']);
            } else {
                $data['product_name'] = sanitize_text_field($_POST['edit_product_name']);
            }

            $wpdb->update(
                $table_name,
                $data,
                array('id' => intval($_POST['edit_id'])),
                array('%f', '%s'),
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
                        echo '<td><button class="edit-btn" data-type="income" data-id="' . esc_attr($row->id) . '">Edit</button> | <a href="?action=sie_delete_record&type=income&id=' . esc_attr($row->id) . '">Delete</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No records found.</p>';
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
                        echo '<td><button class="edit-btn" data-type="expense" data-id="' . esc_attr($row->id) . '">Edit</button> | <a href="?action=sie_delete_record&type=expense&id=' . esc_attr($row->id) . '">Delete</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p>No records found.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Modal for Edit -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Record</h2>
                <form id="editForm" method="post" action="">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="hidden" name="edit_type" id="edit_type">

                    <div id="editFields"></div>

                    <input type="submit" name="sie_edit_submit" value="Update">
                </form>
            </div>
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

        document.querySelector('.tablinks').click();

        // Modal functionality
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                var type = this.getAttribute('data-type');
                var id = this.getAttribute('data-id');
                var editFields = document.getElementById('editFields');

                // Clear previous fields
                editFields.innerHTML = '';

                // Fetch existing data and populate fields
                var xhr = new XMLHttpRequest();
                xhr.open('POST', sieAjax.ajaxurl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var record = response.data;
                        document.getElementById('edit_id').value = record.id;
                        document.getElementById('edit_type').value = type;

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
                    }
                };
                xhr.send('action=sie_get_record&type=' + type + '&id=' + id);
            });
        });

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
<?php
    return ob_get_clean(); // Return the buffered content
}

add_shortcode('sie_report', 'sie_display_income_expenses');

function sie_get_record()
{
    global $wpdb;
    $table_name = $_POST['type'] === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';
    $id = intval($_POST['id']);
    $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if ($record) {
        wp_send_json_success($record);
    } else {
        wp_send_json_error('Record not found');
    }
}

add_action('wp_ajax_sie_get_record', 'sie_get_record');

// Register AJAX handlers for updating records
function sie_update_record() {
    global $wpdb;

    if (isset($_POST['edit_id']) && isset($_POST['edit_type'])) {
        $id = intval($_POST['edit_id']);
        $type = sanitize_text_field($_POST['edit_type']);
        $table_name = $type === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';

        // Prepare data based on type
        $data = array(
            'amount' => floatval($_POST['edit_amount']),
            'date' => sanitize_text_field($_POST['edit_date'])
        );

        if ($type === 'expense') {
            $data['description'] = sanitize_text_field($_POST['edit_description']);
            $data['expenses_date'] = date('Y-m-d', strtotime($_POST['edit_date']));
        } else {
            $data['product_name'] = sanitize_text_field($_POST['edit_product_name']);
            $data['income_date'] = date('Y-m-d H:i:s', strtotime($_POST['edit_date']));
        }

        $updated = $wpdb->update(
            $table_name,
            $data,
            array('id' => $id),
            array('%f', '%s'),
            array('%d')
        );

        if ($updated !== false) {
            wp_send_json_success('Record updated successfully');
        } else {
            wp_send_json_error('Update failed.');
        }
    } else {
        wp_send_json_error('Invalid request.');
    }
}

add_action('wp_ajax_sie_update_record', 'sie_update_record');
add_action('wp_ajax_nopriv_sie_update_record', 'sie_update_record'); // Allow access to non-logged-in users if needed

function sie_delete_record() {
    global $wpdb;
    $table_name = $_GET['type'] === 'expense' ? $wpdb->prefix . 'sie_expenses' : $wpdb->prefix . 'sie_income';
    $id = intval($_GET['id']);

    $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );

    wp_redirect($_SERVER['HTTP_REFERER']);
    exit;
}

add_action('wp_ajax_sie_delete_record', 'sie_delete_record');
add_action('wp_ajax_nopriv_sie_delete_record', 'sie_delete_record'); // Allow access to non-logged-in users if needed

