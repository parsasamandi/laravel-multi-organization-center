<div id="formModal" 
     class="modal fade {{ $english == "true" ? 'text-left' : 'text-right' }}" 
     tabindex="-1" 
     role="dialog" 
     aria-hidden="true" 
     data-english="{{ $english == 'true' ? 'true' : 'false' }}">
  <div class="modal-dialog {{ $size }}">
    <div class="modal-content">
      <div class="modal-header {{ $english == "true" ? 'text-left' : 'text-right' }}">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body {{ $english == "true" ? 'text-left' : 'text-right' }}">
        <form id="{{ $formId }}" 
              class="form-horizontal" 
              style="direction: {{ $english == "true" ? 'ltr' : 'rtl' }};">

          {{ csrf_field() }}

          <span class="form_output"></span>
          {{ $content ?? null }}

          <br />
          <div class="form-group" align="center">
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="button_action" id="button_action" value="insert" />

            <input type="submit" name="submit" value="ثبت" class="btn btn-primary action-button" />

            <button type="button" 
                    class="btn btn-secondary" 
                    data-dismiss="modal">
              {{ $english == "true" ? 'Exit' : 'خروج' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
