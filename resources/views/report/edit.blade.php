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
            <div class="col-md-12 mb-3">
                <!-- Date -->
                <label for="date">تاریخ:</label>
                <select id="general_info_id" name="general_info_id">
                    @php
                    $months = [
                        1 => 'فروردین',
                        2 => 'اردیبهشت',
                        3 => 'خرداد',
                        4 => 'تیر',
                        5 => 'مرداد',
                        6 => 'شهریور',
                        7 => 'مهر',
                        8 => 'آبان',
                        9 => 'آذر',
                        10 => 'دی',
                        11 => 'بهمن',
                        12 => 'اسفند',
                    ];
                    @endphp
                    @foreach ($dates as $date)
                        <option value="{{ $date->id }}">
                            {{ $months[$date->jalaliMonth] }} {{ $date->jalaliYear }}
                        </option>
                    @endforeach
                </select>
            </div>


            <!-- Expenses -->
            <x-input type="number" key="expenses" placeholder="مبلغ هزینه"
                class="col-md-4 mb-3" value="{{ $report['id'] }}" />

            <!-- Range -->
            <x-input type="number" key="range" placeholder="ردیف هزینه"
                class="col-md-4 mb-3" value="{{ $report['range'] }}" />

            <!-- Type -->
            @include('includes.report.type')

            <!-- Description -->
            <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-3" value="{{ $report['description'] }}" />

            <!-- Confirmed or Not confirmed status -->
            @if(Auth::user()->type == 1)
                {{-- Confirmation --}}
                @include('includes.confirmation')
            @endif

        </div>

        <!-- File -->
        <h6>ارسال چاپ صورت حساب بانکی</h6>  
        <input type="file" id="file" name="receipt" class="mb-3"
            accept="application/vnd.ms-excel,
            application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
            text/csv,application/csv"/>

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

        // Report type
        $('#type').val({{ json_encode($report['type']) }}).trigger('change');

        // Default date
        $('#general_info_id').val({{ json_encode($report['general_info_id']) }}).trigger('change');


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
                    $('#form_output').html(response.message);

                    $(window.formId)[0].reset();

                    if(window.dt != null) {
                        window.dt.draw(false);
                    }
                },
                error: function (response) {
                    // Handle error response
                    var data = JSON.parse(response);
                    // Error
                    error_html = '';
                    for(var all in data.errors) {
                        error_html += '<div class="alert alert-danger">' + data.errors[all] + '</div>';
                    }
                    $('#form_output').html(error_html);
                }
            });
        });
    });
</script>
@endsection
