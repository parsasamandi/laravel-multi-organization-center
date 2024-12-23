@extends('layouts.admin')
@section('title', 'فهرست اطلاعات تیم گلستان')

@section('content')

  @include('includes.successModal')

  {{-- Header --}}
  <x-header pageName="تیم گلستان" buttonValue="عضو گلستان" :type="1">
    <x-slot name="table">
      <x-table :table="$golestanTeamTable" />
    </x-slot>
  </x-header>


  {{-- Insertion --}}
  <x-admin.insert size="modal-g" formId="golestanTeamForm" english="false">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        
        <!-- Phone number -->
        <x-input key="phone_number" placeholder="شماره تلفن"
          class="col-md-6 mb-3" required="true"/>

        <!-- Golestan team name -->
        <x-input key="name" placeholder="نام عضو گلستان" 
          class="col-md-6 mb-3" required="true"/>

        <!-- Email -->
        <x-input key="email" placeholder="ایمیل عضو گلستان"
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
  <x-admin.delete title="عضو گلستان" english="false" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $golestanTeamTable->scripts() !!}



<script>
  $(document).ready(function () {

    // Change the English number of pagination to Persian
    $('#golestanTeamTable').DataTable().on('draw', function() {
      convertNumbersToPersian('.dataTables_paginate .paginate_button');
    });

    // Golestan Team Table
    let dt = window.LaravelDataTables['golestanTeamTable'];
    let action = new RequestHandler(dt,'#golestanTeamForm', 'golestanTeam');

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
        url: "{{ url('golestanTeam/edit') }}",
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