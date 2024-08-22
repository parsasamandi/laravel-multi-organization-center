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
        // Empty the form output
        emptyFormOutput();
        // Show the insertion/update modal
        showFormModal();
        // Set the button action for insertion
        $('#button_action').val('insert');
        // Set the button value to 'تایید'
        $('#action').val('تایید');
        // Set the sign * as 'required'
        $('.required-heading .input-required').show();
        // Set the selectbox values to null
        $('select').val(null).trigger('change');
        // Reset the dataTable
        $(window.formId)[0].reset();
        window.dt.cear().draw();
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
                    success(data, "#formModal");
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
                    success(data, "#confirmationModal");
                },
                error: function (data) {
                    error(data);
                }
            })
        });
    }

    // Default edit data
    reloadModal() {
        // Empty the form output
        emptyFormOutput();
        // Show the #formModal
        showFormModal();
    }

    // Edit on success
    editOnSuccess(id) {
        $('#id').val(id);
        // Set the button action for update
        $('#button_action').val('update');
        // Set the button value to 'ثبت تغییرات'
        $('#action').val('ثبت تغییرات');
        // Remove "required field" from heading
        $('.required-heading .input-required').hide();
    }
}

// Show the form modal
function showFormModal() {
    $('#formModal').modal('show');
}

// Empty the form output
function emptyFormOutput() {
    $('.form_output').empty();
}

// Success handler
function success(data, modal) {
    // Hide the current modal
    $(modal).modal('hide');
    // Show the success modal if the modal was #formModal
    modal === "#formModal" && $('#successModal').modal('show');
    // Empty the form output
    emptyFormOutput();
    // Reset the form
    $(window.formId)[0].reset();
    // Check if dataTable is not null, then refresh
    window.dt && window.dt.draw(false);
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
    $('.form_output').html(error_html);
}
