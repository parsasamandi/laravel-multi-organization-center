class RequestHandler {
    // Constructor
    constructor(dt,formId,url) {
        window.dt = dt; // Datatable 
        window.formId = formId; // Form id
        window.url = url; // Url
    }

    // modal
    openInsertionModal() {
        $('#formModal').modal('show');
        $('#button_action').val('insert');
        $('#action').val('تایید');
        $('#form_output').html('');
        $(window.formId)[0].reset();
    }

    // Insert
    insert() {
        // Store or Update
        $(window.formId).on('submit', function (event) {
            event.preventDefault();
            // Form Data
            var form_data = new FormData(this);
            // Get the file input element
            var fileInput = document.getElementById('file');
            // Get the file from the file input
            var file = fileInput.files[0];
            // Check if a file exists
            if (file) {
                // Append the file to the FormData object
                form_data.append('file', file);
            }

            $.ajax({
                url: "/" + window.url + "/store",
                method: "POST",
                contentType: false,
                processData: false,
                cache: false,
                data: form_data,
                success: function (data) { 
                    success(data);
                },
                error: function (data) {
                    error(data);
                }
            })
        });
    }

    // Delete
    delete(id) {
        $('#confirmationModal').modal('show'); // Confirm
        $('#ok_button').click(function () {
            $.ajax({
                url: "/" + window.url + "/delete/" + id,
                method: "get",
                success: function(data) {
                    $('#confirmationModal').modal('hide');
                    window.dt.draw(false);
                }
            })
        });
    }

    // Default edit data
    reloadModal() {
        $('#form_output').html('');
        $('#formModal').modal('show');
    }

    // Edit on success
    editOnSuccess(id) {
        $('#id').val(id);
        $('#button_action').val('update');
        $('#action').val('ویرایش');
    }
}

// Success
function success(data) {
    $('#form_output').html(data.message);
    $(window.formId)[0].reset();
    if(window.dt != null) {
        window.dt.draw(false);
    }
}

// Error
function error(data) {
    // Check if responseText is empty or undefined
    if (!data.responseText || data.responseText.trim() === '') {
        $('#form_output').html('<div class="alert alert-danger">متاسفانه اطلاعاتی فرستاده نشد.</div>');
        return;
    }
    
    // Parse responseText to JSON
    var jsonData;
    try {
        jsonData = JSON.parse(data.responseText);
    } catch (e) {
        $('#form_output').html('<div class="alert alert-danger">خطایی رخ داده است، لطفا دوباره امتحان کنید.</div>');
        return;
    }
    
    // Handle JSON data
    var error_html = '';
    for (var all in jsonData.errors) {
        error_html += '<div class="alert alert-danger">' + jsonData.errors[all] + '</div>';
    }
    $('#form_output').html(error_html);
}
