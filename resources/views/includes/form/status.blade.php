@if(Auth::user()->type == 1)
    <form id="statusForm">
        {{ csrf_field() }}

        {{-- Output --}}
        <span id="form_output"></span>

        <!-- Id -->
        <input type="hidden" name="id" id="id" value="{{ $id }}"  />

        <div class="row">
            <div class="col-md-4 mt-2 mb-2">
                <!-- Confirmed or Not confirmed status -->
                @include('includes.confirmation')
            </div>
        </div>
            
        <!-- Confirm Status -->
        <button type="button" type="submit" id="confirmStatus" class="btn btn-primary">ثبت وضعیت</button>

    </form>
@endif