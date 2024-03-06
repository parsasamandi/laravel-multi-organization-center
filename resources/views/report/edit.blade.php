@extends('layouts.admin')
@section('title', 'ویرایش گزارش جزئیات هزینه‌کرد')

@section('content')

<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
      <h5>ویرایش گزارش یک ردیف هزینه‌کرد</h5>
    </ol>

    <form id="reportEditForm" class="form-horizontal details-form" enctype="multipart/form-data">
        {{ csrf_field() }}

        <span id="form_output"></span>

        <div class="row">

            <!-- Jalali Years -->
            <div class="col-md-4 mb-3">
                @include('includes.jalaliYearsSelectBox')
            </div>

            <!-- Jalali Months -->
            <div class="col-md-4">
                @include('includes.jalaliMonthsSelectBox')
            </div>

            <!-- Expenses -->
            <x-input type="number" key="expenses" placeholder="مبلغ هزینه"
                class="col-md-4 mb-3" value="{{ $report['id'] }}" />

            <!-- Range -->
            <x-input type="number" key="range" placeholder="ردیف هزینه"
                class="col-md-6 mb-3" value="{{ $report['range'] }}" />

            <div class="col-md-6 mb-3">
                <!-- Type -->
                <label for="type">نوع گزارش:</label>
                <select name="type" id="type">
                    <option value="1">هزینه حقوق کارمندان</option>
                    <option value="2">هزینه آموزش</option>
                    <option value="3">هزینه های سلامت</option>
                </select>
            </div>

            <!-- Description -->
            <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-3" value="{{ $report['description'] }}" />

        </div>
        
        <!-- File -->
        <h6>ارسال چاپ صورت حساب بانکی</h6>  
        <input type="file" id="file" name="receipt" class="mb-3"
            accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,application/csv"/>

        <br/>

            <!-- Buttons -->
            <div class="form-group" align="center">
                <input type="submit" id="action" value="ویرایش" class="btn btn-primary" />
                <button type="button" id="return_button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>
                <input type="hidden" name="id" id="id" value="{{ $report['id'] }}"  />
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
        $('#jalaliMonth').val({{ json_encode($report['jalaliMonth']) }}).trigger('change');

        // Jalali year
        $('#jalaliYear').val({{ json_encode($report['jalaliYear']) }}).trigger('change');

        // Report type
        $('#type').val({{ json_encode($report['type']) }}).trigger('change');


        // Form submission for updating
        $('#reportEditForm').on('submit', function (event) {
          event.preventDefault();

          var formData = new FormData(this);
          formData.append('file', formData);

            // AJAX request
            $.ajax({
                url: '/report/update', 
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
                    error(response);
                }
            });
        });
    });
</script>
@endsection
