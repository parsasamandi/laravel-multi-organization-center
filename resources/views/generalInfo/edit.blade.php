@extends('layouts.admin')
@section('title', 'صفحه ویرایش مقدمات گزارش')

@section('content')

<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
      <li class="breadcrumb-item">ویرایش مقدمات گزارش</li>
    </ol>

    <form id="generalInfoEditForm" class="form-horizontal details-form" enctype="multipart/form-data">
        {{ csrf_field() }}

        {{-- Output --}}
        <span id="form_output"></span>

        <div class="row">
            <div class="col-md-4 mb-3">
                @include('includes.jalaliYearsSelectBox')
            </div>

            <div class="col-md-4 mb-3">
                @include('includes.jalaliMonthsSelectBox')
            </div>

            <x-input type="number" key="bank_balance" placeholder="موجودی در پایان ماه"
                class="col-md-4 mb-3" value="{{ $generalInfo->bank_balance }}"/>

            <!-- Confirmed or Not confirmed status -->
            @if(Auth::user()->type == 1)
                {{-- Confirmation --}}
                @include('confirmation')
            @endif
        </div>

        <!-- File -->
        <h6>ارسال چاپ صورت حساب بانکی</h6>
        <input type="file" id="file" name="receipt" class="mb-3" 
            accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,application/csv"/>


        {{-- Buttons --}}
        <div class="form-group" align="center">
            <input type="submit" id="action" value="ویرایش" class="btn btn-primary" />
            <button type="button" id="return_button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>
            <input type="hidden" name="id" id="id" value="{{ $generalInfo->id }}"  />
            <input type="hidden" name="button_action" id="button_action" value="update" />
        </div>
    </form>

</div>


@endsection

@section('scripts')
@parent

<script>
    $(document).ready(function () {

        // Jalali month
        $('#jalaliMonth').val({{ json_encode($generalInfo->jalaliMonth) }}).trigger('change');
        // Jalali year
        $('#jalaliYear').val({{ json_encode($generalInfo->jalaliYear) }}).trigger('change');

        // Form submission for updating
        $('#generalInfoEditForm').on('submit', function (event) {
          event.preventDefault();

          var formData = new FormData(this);
          formData.append('file', formData);

            // AJAX request
            $.ajax({
                url: '/generalInfo/update', 
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                  // Handle success response
                  success(response);
                },
                error: function (response) {
                    // Handle error response
                    // error(response);
                }
            });
        });
    });
</script>
@endsection
