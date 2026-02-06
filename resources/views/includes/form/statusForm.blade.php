@if(Auth::user()->type == 1)
    <form id="statusForm">
        {{ csrf_field() }}

        {{-- Output --}}
        <span id="form_output"></span>

        <!-- Id -->
        <input type="hidden" name="id" id="id" value="{{ $id }}" />

        <div class="row">
            <div class="col-md-4 mt-2 mb-2">
                <!-- Confirmation status -->
                <label for="status">وضعیت بررسی</label>
                <select name="status" id="status">
                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>بررسی نشده</option> <!-- Not reviewed -->
                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>موفق</option> <!-- Successful -->
                    <option value="2" {{ $status == 2 ? 'selected' : '' }}>ناموفق</option> <!-- Unsuccessful -->
                </select>
            </div>
        </div>
            
        <!-- Confirm Status -->
        <button type="button" type="submit" id="confirmStatus" class="btn btn-primary">ثبت وضعیت</button>
    </form>
@endif
