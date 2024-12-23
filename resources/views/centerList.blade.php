@extends('layouts.admin')
@section('title', 'فهرست اطلاعات مرکز')

@section('content')

  @include('includes.successModal')

  {{-- Header --}}
  <x-header pageName="مراکز" buttonValue="مرکز" :type="1">
    <x-slot name="table">
      <x-table :table="$centerTable" />
    </x-slot>
  </x-header>


  {{-- Insertion --}}
  <x-admin.insert size="modal-g" formId="centerForm" english="false">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <!-- Center code -->
        <x-input key="code" placeholder="کد مرکز" 
          class="col-md-6 mb-3" required="true"/>

        <!-- Phone number -->
        <x-input key="phone_number" placeholder="شماره تلفن"
          class="col-md-6 mb-3" required="true"/>

        <!-- Center name -->
        <x-input key="name" placeholder="نام مرکز" 
          class="col-md-12 mb-3" required="true"/>

        <!-- Center name in English -->
        <x-input key="name_en" placeholder="نام مرکز" 
          class="col-md-12 mb-3" required="true"/>

        <!-- Email -->
        <x-input key="email" placeholder="ایمیل مرکز"
          class="col-md-12 mb-3" required="true"/>

        <!-- Password -->
        <div class="col-md-6 mb-3">
          <label for="password" class="required-heading">رمز: <span class="input-required">*</span></label>
          <input name="password" id="password" class="form-control" 
            placeholder="رمز" autocomplete="new-password">
        </div>
        <div class="col-md-6">
          <label for="password-confirm" class="required-heading">تکرار رمز: <span class="input-required">*</span></label>
          <input name="password-confirm" id="password-confirm" class="form-control"  
            placeholder="تکرار رمز" autocomplete="new-password">
        </div>
      </div>
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="مرکز" english="false" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $centerTable->scripts() !!}


<script>
  $(document).ready(function () {

    // Change the English number of pagination to Persian
    $('#centerTable').DataTable().on('draw', function() {
      convertNumbersToPersian('.dataTables_paginate .paginate_button');
    });

    // Center Table
    let dt = window.LaravelDataTables['centerTable'];
    let action = new RequestHandler(dt,'#centerForm', 'center');

    // Record modal
    $('#create_record').click(function () {
      action.openInsertionModal();
    });

    // Insert
    action.insert();

    // Delete
    window.showConfirmationModal = function showConfirmationModal(url) {
      action.delete(url);
    }

    // Edit
    window.showEditModal = function showEditModal(url) {
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('center/edit') }}",
        method: 'get',
        data: { id: url },
        success: function (data) {  
          action.editOnSuccess(url);
          $('#code').val(data.code);
          $('#name').val(data.name);
          $('#email').val(data.email);
          $('#phone_number').val(data.phone_number);
          $('#type').val(data.type).trigger('change');
        }
      })
    }
  });
</script>
@endsection