@extends('layouts.admin')
@section('title', 'ویرایش گزارشات موجودی و صورت حساب')

@php
    // $action is an instance of the Action class
    $action = new \App\Providers\Action();
@endphp

@section('content')

@php
    // $action is an instance of the Action class
    $action = new \App\Providers\Action();
@endphp

<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
      <li class="breadcrumb-item">ویرایش گزارشات موجودی و صورت حساب</li>
    </ol>

    <form id="generalInfoEditForm" class="form-horizontal details-form" enctype="multipart/form-data">
        {{ csrf_field() }}

        {{-- Output --}}
        <span id="form_output"></span>

        <div class="row">
            <x-input key="bank_balance" placeholder="موجودی در پایان ماه (ریال)" 
                class="col-md-4 mb-3" value="{{ $action->englishToPersianNumbers($generalInfo->bank_balance) }}"/>
        </div>

        <!-- File -->
        <h6>ارسال چاپ صورت حساب بانکی</h6>
        <input type="file" id="file" name="receipt" class="mb-3" 
            accept=".pdf,.doc,.docx,.csv,application/msword,application/
            vnd.openxmlformats-officedocument.wordprocessingml.document,application/
            vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>

        <br/>

        {{-- Buttons --}}
        <div class="form-group" align="center">
            <input type="submit" id="action" value="ویرایش" class="btn btn-primary" />
            <button type="button" id="return_button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>
            <input type="hidden" name="id" id="id" value="{{ $generalInfo->id }}"  />
        </div>
    </form>

</div>


@endsection

@section('scripts')
@parent

<script>
    $(document).ready(function () {
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
                    error(response);
                }
            });
        });
    });
</script>
@endsection