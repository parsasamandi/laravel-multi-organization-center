@extends('layouts.admin')
@section('title', 'لیست اطلاعات کلی')

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
        <x-input key="year" placeholder="سال" 
          class="col-md-6 mb-3" />

        <x-input key="month" placeholder="ماه"
          class="col-md-6 mb-3" />

        <x-input key="bank_balance" placeholder="موجودی در پایان ماه"
          class="col-md-12 mb-3" />

      </div>
      <!-- File -->
      <h6 class="images">ارسال پرینت صورت حساب بانکی</h6>
      <input class="mb-3" type="file" id="file" name="bank_statement_receipt" 
        accept="application/pdf,image/*,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="اطلاعات" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
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
          action.editData($url);
          $('#number').val(data.number);
          $('#products').val(data.product_id)
          $('#status').val(data.status).trigger('change');
        }
      })
    }
  });
</script>
@endsection
