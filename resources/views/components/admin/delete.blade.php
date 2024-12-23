<!-- Delete modal -->
<div id="confirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <!-- Form output -->
                <span class="form_output"></span>
                @if($english == "false")
                    <!-- Title to display for deleting -->
                    <h5 class="alert alert-danger mb-0">آیا مایل به حذف {{ $title }} هستید؟ </h5>
                @else 
                    <h5 class="alert alert-danger mb-0">Please confirm to delete {{ $title }}</h5>
                @endif
            </div>

            <div class="modal-footer {{ $english == "true" ? 'ltr' : 'rtl' }}">
                <button type="button" id="delete_confirmation" class="btn btn-primary action-button">
                    {{ $english == "true" ? 'Confirm' : 'تایید' }}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {{ $english == "true" ? 'Exit' : 'خروج' }}
                </button>
            </div>
        </div>
    </div>
</div>