class RequestHandler {
    constructor(dt, formId, url, language = "Persian") {
        window.dt = dt; // DataTable instance
        window.formId = formId; // Form ID
        window.url = url; // URL
        window.language = language; // URL
    }

    // Modal
    openInsertionModal() {
        $(window.formId)[0].reset(); // Reset the form
        $('select').val(null).trigger('change'); // Reset select inputs
        $('.required-heading .input-required').show(); // Show required field indicators
        this.reloadModal(); // Reload modal
        window.dt.clear().draw(); // Clear the DataTable
    }

    // Insertion (or Update)
    insert() {
        $(window.formId).off('submit').on('submit', function (event) {
            event.preventDefault();

            // Disable the submit button
            toggleButton(false);

            const formData = new FormData(event.target); // Form data

            $.ajax({
                url: `/${window.url}/store`,
                method: 'POST',
                contentType: false,
                processData: false,
                cache: false,
                data: formData,
                success: function (data) {
                    success(data, '#formModal'); // Handle success
                },
                error: function (xhr) {
                    error(xhr); // Handle error
                }
            });
        });
    }

    // Delete
    delete(id) {
        emptyFormOutput(); // Clear the form output
        $('#confirmationModal').modal('show'); // Show confirmation modal

        $('#delete_confirmation').off().on('click', function () {
            toggleButton(false);

            $.ajax({
                url: `/${window.url}/delete`,
                method: 'GET',
                data: { id: id },
                success: function (data) {
                    success(data, '#confirmationModal'); // Handle success
                },
                error: function (xhr) {
                    error(xhr); // Handle error
                }
            });
        });
    }

    // Reload Modal
    reloadModal() {
        emptyFormOutput(); // Clear form output
        showFormModal(); // Show the form modal
        toggleButton(true); // Enable buttons
    }

    // Edit on Success
    editOnSuccess(id) {
        $('#id').val(id); // Set ID for editing
        $('#button_action').val('update'); // Set action to update
        $('.required-heading .input-required').hide(); // Hide required indicators
    }
}

// Utility Functions

// Show the form modal
function showFormModal() {
    $('#formModal').modal('show');
}

// Empty the form output
function emptyFormOutput() {
    $('.form_output').empty();
}

// Toggle the submit button state
function toggleButton(enable = true) {
    const button = $('.action-button');
    
    if (window.language === 'English') {
        // If the language is English, set button text as "Submit" and "Submitting"
        button.val(enable ? 'Submit' : 'Submitting...');
    } else {
        // If the language is not English (default Persian), set button text to Persian
        button.val(enable ? 'ثبت' : 'در حال ثبت');
    }

    button.prop('disabled', !enable); // Disable or enable the button
}

// Success Handler
function success(data, modal) {
    $(modal).modal('hide'); // Hide the modal
    if (modal === '#formModal') $('#successModal').modal('show'); // Show success modal
    emptyFormOutput(); // Clear form output
    $(window.formId)[0].reset(); // Reset the form
    window.dt && window.dt.ajax.reload(null, false); // Reload DataTable
    toggleButton(true); // Re-enable buttons
}

// Error Handler
function error(xhr) {
    let errorHtml = '';
    if (xhr.responseJSON?.errors) {
        Object.values(xhr.responseJSON.errors).forEach((errors) => {
            errorHtml += errors.map((err) => `<div class="alert alert-danger">${err}</div>`).join('');
        });
    } else if (xhr.responseJSON?.message) {
        errorHtml = `<div class="alert alert-danger">${xhr.responseJSON.message}</div>`;
    } else {
        errorHtml = '<div class="alert alert-danger">An unexpected error occurred.</div>';
    }

    $('.form_output').html(errorHtml); // Display errors
    toggleButton(true); // Re-enable buttons
}
