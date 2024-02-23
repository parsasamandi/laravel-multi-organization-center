@extends('layouts.admin')
@section('title', 'لیست مرکز')

@section('content')

  {{-- Header --}}
  <x-header pageName="لیست مرکز" buttonValue="لیست مرکز">
    <x-slot name="table">
      <x-table :table="$centerTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="centerForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <x-input key="name" placeholder="نام مرکز" 
          class="col-md-12 mb-3" />

        <x-input key="email" placeholder="ایمیل مرکز"
          class="col-md-12 mb-3" />

        <x-input key="phone_number" placeholder="شماره تلفن"
          class="col-md-12 mb-3" />

          {{-- Passwords --}}
        <div class="col-md-12 mb-3">
          <label for="password">رمز جدید:</label>
          <input type="password" name="password" id="password" class="form-control" 
            placeholder="رمز جدید" autocomplete="new-password">
        </div>
        <div class="col-md-12">
          <label for="password-confirm">تکرار رمز جدید:</label>
          <input type="password" name="password-confirm" id="password-confirm" class="form-control"  
            placeholder="تکرار رمز جدید" autocomplete="new-password">
        </div>
      </div>

          
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="مرکز" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $centerTable->scripts() !!}

<script>
  $(document).ready(function () {
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
      edit(url);
    }
    function edit($url) {
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('center/edit') }}",
        method: 'get',
        data: { id: $url },
        success: function (data) {  
          action.editOnSuccess($url);
          $('#name').val(data.name);
          $('#phone_number').val(data.phone_number);
          $('#email').val(data.email);
          $('#password').val('new_password');
          $('#password-confirm').val('new_password');
        }
      })
    }
  });
</script>
@endsection
