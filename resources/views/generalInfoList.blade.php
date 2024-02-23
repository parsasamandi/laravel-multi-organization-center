@extends('layouts.admin')
@section('title', 'لیست ')

@section('content')

  {{-- Header --}}
  <x-header pageName="اطلاعات کلی" buttonValue="اطلاعات کلی">
    <x-slot name="table">
      <x-table :table="$generalInfoTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="generalInfoForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">

        <div class="col-md-6 mb-3">
          @include('includes.jalaliYearsSelectBox')
        </div>

        <div class="col-md-6 mb-3">
          @include('includes.jalaliMonthsSelectBox')
        </div>

        <x-input type="number" key="bank_balance" placeholder="موجودی در پایان ماه"
          class="col-md-12 mb-3"/>

      </div>
      <!-- File -->
      <h6>ارسال پرینت صورت حساب بانکی</h6>
      <input type="file" id="file" name="bank_statement_receipt" class="mb-3" 
        accept="application/pdf,image/*,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>

    </x-slot>
  </x-admin.insert>


  {{-- Delete --}}
  <x-admin.delete title="اطلاعات کلی" />

  
@endsection

@section('scripts')
@parent

<!-- General Info Data Table -->
{!! $generalInfoTable->scripts() !!}

<script>
  $(document).ready(function () {
    // phoneNumber Table
    let dt = window.LaravelDataTables['generalInfoTable'];
    let action = new RequestHandler(dt,'#generalInfoForm', 'generalInfo');

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
        url: "{{ url('generalInfo/edit') }}",
        method: 'get',
        data: { id: $url },
        success: function (data) {  
          action.editOnSuccess($url);
          $('#bank_balance').val(data.bank_balance);
          $('#jalaliMonth').val(data.jalaliMonth).trigger('change');
          $('#jalaliYear').val(data.jalaliYear).trigger('change');
        }
      })
    }
  });
</script>
@endsection
