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

    $('#editForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: sieAjax.ajaxurl,
            method: 'POST',
            data: $(this).serialize() + '&action=sie_update_record',
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    location.reload(); // Refresh the page to reflect changes
                } else {
                    alert(response.data);
                }
            }
        });
    });

    $('.close').click(function() {
        $('#editModal').hide();
    });

    $(window).click(function(event) {
        if ($(event.target).is('#editModal')) {
            $('#editModal').hide();
        }
    });
});


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