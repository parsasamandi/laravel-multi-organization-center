class RequestHandler {
    // Constructor
    constructor(dt, formId, url) {
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
        $(window.formId).on('submit', (event) => {

            event.preventDefault();
            // Form Data
            const form_data = new FormData(event.target);; // Include all form data

            $.ajax({
                url: "/" + window.url + "/store",
                method: "POST",
                contentType: false,
                processData: false,
                cache: false,
                data: form_data,
                success: (data) => this.handleSuccess(data),
                error: (data) => this.handleError(data),
            });
        });
    }

    // Delete
    delete(id) {
        $('#confirmationModal').modal('show'); // Confirm

        $('#ok_button').off('click').on('click', () => {
            $.ajax({
                url: "/" + window.url + "/delete",
                method: "GET",
                data: { id: id },
                success: (data) => {
                    $('#confirmationModal').modal('hide');
                    window.dt.draw(false);
                }
            });
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

    // Success handler
    handleSuccess(data) {
        $('#formModal').modal('hide');
        $('#successModal').modal('show');
        $(window.formId)[0].reset();
        if (window.dt != null) {
            window.dt.draw(false);
        }
    }

    // Error handler
    handleError(data) {
        // Parse to JSON
        const errors = JSON.parse(data.responseText).errors;
        let errorHtml = '';
        for (const key in errors) {
            errorHtml += `<div class="alert alert-danger">${errors[key]}</div>`;
        }
        $('#form_output').html(errorHtml);
    }
}
