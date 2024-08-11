jQuery(document).ready(function($) {
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
});
