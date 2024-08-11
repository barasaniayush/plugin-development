jQuery(document).ready(function($) {
    // Handle Edit Button Click
    $(document).on('click', '.edit-btn', function() {
        var type = $(this).data('type');
        var id = $(this).data('id');
        var editFields = $('#editFields');

        $.ajax({
            url: sieAjax.ajaxurl,
            method: 'POST',
            data: {
                action: 'sie_get_record',
                type: type,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    var record = response.data;
                    $('#edit_id').val(record.id);
                    $('#edit_type').val(type);

                    if (type === 'expense') {
                        editFields.html(`
                            <label for="edit_description">Expense Description:</label>
                            <input type="text" id="edit_description" name="edit_description" value="${record.description}" required>
                            
                            <label for="edit_amount">Amount Spent:</label>
                            <input type="number" id="edit_amount" name="edit_amount" step="0.01" value="${record.expenses_amount}" required>
                            
                            <label for="edit_date">Date:</label>
                            <input type="date" id="edit_date" name="edit_date" value="${record.expenses_date}" required>
                        `);
                    } else {
                        editFields.html(`
                            <label for="edit_product_name">Product Name:</label>
                            <input type="text" id="edit_product_name" name="edit_product_name" value="${record.product_name}" required>
                            
                            <label for="edit_amount">Amount Collected:</label>
                            <input type="number" id="edit_amount" name="edit_amount" step="0.01" value="${record.income_amount}" required>
                            
                            <label for="edit_date">Date:</label>
                            <input type="date" id="edit_date" name="edit_date" value="${record.income_date}" required>
                        `);
                    }

                    $('#editModal').show();
                } else {
                    alert(response.data);
                }
            }
        });
    });

    // Handle Update Form Submission
    $('#editModal form').submit(function(e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way

        var formData = $(this).serialize();

        $.ajax({
            url: sieAjax.ajaxurl,
            method: 'POST',
            data: formData + '&action=sie_edit_record',
            success: function(response) {
                if (response.success) {
                    alert('Record updated successfully.');
                    $('#editModal').hide(); // Hide the modal
                    
                    // Update the table row without refreshing the page
                    var type = $('#edit_type').val();
                    var id = $('#edit_id').val();
                    var row = $(`.edit-btn[data-id="${id}"]`).closest('tr');

                    var updatedData = response.data;
                    if (type === 'expense') {
                        row.html(`
                            <td>${updatedData.description}</td>
                            <td>${updatedData.expenses_amount}</td>
                            <td>${updatedData.expenses_date}</td>
                            <td>
                                <button class="edit-btn" data-type="expense" data-id="${updatedData.id}">Edit</button>
                                <button class="delete-btn" data-type="expense" data-id="${updatedData.id}">Delete</button>
                            </td>
                        `);
                    } else {
                        row.html(`
                            <td>${updatedData.product_name}</td>
                            <td>${updatedData.income_amount}</td>
                            <td>${updatedData.income_date}</td>
                            <td>
                                <button class="edit-btn" data-type="income" data-id="${updatedData.id}">Edit</button>
                                <button class="delete-btn" data-type="income" data-id="${updatedData.id}">Delete</button>
                            </td>
                        `);
                    }
                } else {
                    alert('Failed to update the record.');
                }
            }
        });
    });

    // Handle Delete Button Click
    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this record?')) {
            var type = $(this).data('type');
            var id = $(this).data('id');

            $.ajax({
                url: sieAjax.ajaxurl,
                method: 'POST',
                data: {
                    action: 'sie_delete_record',
                    type: type,
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        alert('Record deleted successfully.');
                        $(`.delete-btn[data-id="${id}"]`).closest('tr').remove(); // Remove the row from the table
                    } else {
                        alert('Failed to delete the record.');
                    }
                }
            });
        }
    });

    // Close the modal
    $('.close').click(function() {
        $('#editModal').hide();
    });

    $(window).click(function(event) {
        if ($(event.target).is('#editModal')) {
            $('#editModal').hide();
        }
    });
});


// Function to update table data without refreshing the page
function updateTable() {
    $.ajax({
        url: sieAjax.ajaxurl,
        method: 'POST',
        data: {
            action: 'sie_get_all_records'
        },
        success: function(response) {
            if (response.success) {
                var incomeTable = $('#incomeRecords table');
                var expenseTable = $('#expenseRecords table');

                // Update income records
                incomeTable.html(`
                    <tr><th>Product Name</th><th>Amount</th><th>Date</th><th>Actions</th></tr>
                `);
                $.each(response.data.income, function(index, record) {
                    incomeTable.append(`
                        <tr>
                            <td>${record.product_name}</td>
                            <td>${record.income_amount}</td>
                            <td>${record.income_date}</td>
                            <td>
                                <button class="edit-btn" data-type="income" data-id="${record.id}">Edit</button>
                                <button class="delete-btn" data-type="income" data-id="${record.id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });

                // Update expense records
                expenseTable.html(`
                    <tr><th>Description</th><th>Amount</th><th>Date</th><th>Actions</th></tr>
                `);
                $.each(response.data.expenses, function(index, record) {
                    expenseTable.append(`
                        <tr>
                            <td>${record.description}</td>
                            <td>${record.expenses_amount}</td>
                            <td>${record.expenses_date}</td>
                            <td>
                                <button class="edit-btn" data-type="expense" data-id="${record.id}">Edit</button>
                                <button class="delete-btn" data-type="expense" data-id="${record.id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                alert('Failed to load records.');
            }
        }
    });
}
