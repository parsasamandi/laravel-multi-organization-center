<!-- Delete modal -->
<div id="confirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <h5 class="alert alert-danger mb-0">آیا مایل به حذف {{ $title }} هستید؟</h5>
                <!-- <div class="alert alert-danger">اطلاعات با موفقیت ثبت شد</div> -->
            </div>

            <div class="modal-footer">
                <button type="button" name="ok_button" id="ok_button" class="btn btn-primary">تایید</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
            </div>
        </div>
    </div>
</div>
