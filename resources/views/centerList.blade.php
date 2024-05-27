@extends('layouts.admin')
@section('title', 'فهرست اطلاعات مرکز')

@section('content')

  {{-- Header --}}
  <div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <h2 class="mt-4">فهرست مرکز</h2>
    <ol class="breadcrumb mb-4 right-text">
        <li class="breadcrumb-item">صفحه مرکز</li>
    </ol>

    {{-- Conditional rendering based on user type --}}
    @if(Auth::user()->type == 1)
        {{-- Button for super admin --}}
        <button type="button" id="create_record" class="btn btn-primary btn-sm">افزودن مرکز</button>
        <hr>
    @endif
    
    {{-- Responsive Table --}}
    <div class="table-responsive">
        {!! $centerTable->table(['class' => 'table table-bordered table-striped w-100 nowrap text-center'], false) !!}
    </div>
  </div>


  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="centerForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <!-- Center name -->
        <x-input key="name" placeholder="نام مرکز" 
          class="col-md-12 mb-3" />

        <!-- Email -->
        <x-input key="email" placeholder="ایمیل مرکز"
          class="col-md-12 mb-3" />

        <!-- Phone number -->
        <x-input key="phone_number" placeholder="شماره تلفن"
          class="col-md-12 mb-3" />

        <!-- Type -->
        <div class="col-md-12 mb-2">
          <label for="type">نوع ادمین:</label>
          <select id="type" name="type">
            <option value="0">مرکز</option>
            <option value="1">تیم گلستان</option>
          </select>
        </div>

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
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('center/edit') }}",
        method: 'get',
        data: { id: url },
        success: function (data) {  
          action.editOnSuccess(url);
          $('#name').val(data.name);
          $('#email').val(data.email);
          $('#phone_number').val(data.phone_number);
          $('#type').val(data.type).trigger('change');
          $('#password').val('Password');
          $('#password-confirm').val('Password');
        }
      })
    }

    


  });
</script>
@endsection
