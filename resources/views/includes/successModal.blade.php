@php
  $english = $english ?? false; // Default to false if not passed
@endphp

<div id="successModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <!-- <h5 class="modal-title">Modal title</h5> -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <h5 class="alert alert-success mb-0">
          @if($english == true)
            The data was submitted successfully
          @else
            اطلاعات با موفقیت ثبت شد
          @endif
        </h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          @if($english == true)
            Exit
          @else
            خروج
          @endif
        </button>
      </div>
    </div>
  </div>
</div>
