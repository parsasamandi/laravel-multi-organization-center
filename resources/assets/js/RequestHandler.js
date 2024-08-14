// Efficient process of handling ajax requests
class RequestHandler {
    // Constructor
    constructor(dt,formId,url) {
        window.dt = dt; // Datatable 
        window.formId = formId; // Form id
        window.url = url; // Url
    }

    // Modal
    openInsertionModal() {
        $('#formModal').modal('show');
        $('#button_action').val('insert');
        $('#action').val('تایید');
        $('#form_output').html('');
        $('.required-heading .input-required').show();
        $(window.formId)[0].reset();
        window.dt.clear().draw();
    }

    // Insertion
    insert() {
        // Store or Update
        $(window.formId).on('submit', function (event) {
            event.preventDefault();
            // Form Data
            const form_data = new FormData(event.target); // Include all form data

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
                url: "/" + window.url + "/delete",
                method: "get",
                data: { id: id },
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
        $('#action').val('ثبت تغییرات');
        // Remove "required field" from heading
        $('.required-heading .input-required').hide();
    }
}

// Success
function success(data) {
    $('#formModal').modal('hide');
    $('#successModal').modal('show');
    // $('#form_output').html(data.message);
    $(window.formId)[0].reset();
    if(window.dt != null) {
        window.dt.draw(false);
    }
}

// Error Handler
function error(data) {
    // Parse the JSON response
    const response = JSON.parse(data.responseText);

    // Initialize error HTML
    let error_html = '';

    // Add field-specific errors if they exist
    if (response.errors) {
        Object.values(response.errors).forEach(errors => {
            error_html += errors.map(error => `<div class="alert alert-danger">${error}</div>`).join('');
        });
    }

    // If there are no field-specific errors, show the response.message
    if (!error_html && response.message) {
        error_html = `<div class="alert alert-danger">${response.message}</div>`;
    }

    // Display all errors in the form_output element
    $('#form_output').html(error_html);
}
