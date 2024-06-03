@extends('layouts.admin')
@section('title', 'لیست اطلاعات مرکز')

@section('content')

  {{-- Header --}}
  <div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <h2 class="mt-4">لیست مرکز</h2>

    {{-- Conditional rendering based on user type --}}
    @if(Auth::user()->type == 1)
        {{-- Button for super admin --}}
        <button type="button" id="create_record" class="btn btn-primary btn-sm">+ افزودن مرکز</button>
        <hr>
    @endif
    
    {{-- Responsive Table --}}
    <div class="table-responsive">
        {!! $centerTable->table(['class' => 'table table-bordered table-striped w-100 nowrap text-center'], false) !!}
    </div>
  </div>


  {{-- Insertion --}}
  <x-admin.insert size="modal-lg" formId="centerForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <!-- Center code -->
        <x-input key="code" placeholder="کد مرکز" 
          class="col-md-4 mb-3" required="true"/>

        <!-- Center name -->
        <x-input key="name" placeholder="نام مرکز" 
          class="col-md-4 mb-3" required="true"/>

        <!-- Email -->
        <x-input key="email" placeholder="ایمیل مرکز"
          class="col-md-4 mb-3" required="true"/>

        <!-- Phone number -->
        <x-input key="phone_number" placeholder="شماره تلفن"
          class="col-md-6 mb-3" required="true"/>

        <!-- Type -->
        <div class="col-md-6 mb-2">
          <label for="type">نوع کاربر: <span class="input-required">*</span></label>
          <select id="type" name="type">
            <option value="0">مرکز</option>
            <option value="1">ادمین (گلستان)</option>
          </select>
        </div>

        {{-- Passwords --}}
        <div class="col-md-6 mb-3">
          <label for="password" class="required-heading">رمز جدید: <span class="input-required">*</span></label>
          <input type="password" name="password" id="password" class="form-control" 
            placeholder="رمز جدید" autocomplete="new-password">
        </div>

        <div class="col-md-6">
          <label for="password-confirm" class="required-heading">تکرار رمز جدید: <span class="input-required">*</span></label>
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
