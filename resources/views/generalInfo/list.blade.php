@extends('layouts.admin')
@section('title', 'لیست گزارشات موجودی و صورتحساب')

@section('content')

  {{-- Success Modal To Show After Insertion --}}
  @include('includes.successModal')

  {{-- Header --}}
  <x-header pageName="صورتحساب بانکی" buttonValue="صورتحساب جدید" :type="0">
    <x-slot name="table">
      <x-table :table="$generalInfoTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="generalInfoForm" english="false">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <div class="col-md-6 mb-3">
          @include('includes.jalaliYearsSelectBox')
        </div>

        <div class="col-md-6 mb-3">
          @include('includes.jalaliMonthsSelectBox')
        </div>

        <x-input key="bank_balance" placeholder="موجودی در پایان ماه (ریال)" 
          class="col-md-12 mb-3" required="true"/>
      </div>
      <!-- File -->
      <h6 class="required-heading">پیوست فایل صورتحساب بانکی <span class="input-required">*</span></h6>
      <input type="file" id="file" name="receipt" class="mb-3" 
        accept=".pdf,.doc,.docx,.csv,.xls,application/msword,application/
        vnd.openxmlformats-officedocument.wordprocessingml.document,application/
        vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>

    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="صورتحساب" english="false" />

@endsection

@section('scripts')
@parent

<!-- General Info Data Table -->
{!! $generalInfoTable->scripts() !!}

<script>
  $(document).ready(function () {

    // Change the English number of pagination to Persian number
    $('#generalInfoTable').DataTable().on('draw', function() {
      convertNumbersToPersian('.dataTables_paginate .paginate_button');
    });

    // Datatable
    let dt = window.LaravelDataTables['generalInfoTable'];
    let action = new RequestHandler(dt,'#generalInfoForm', 'generalInfo');

    // Record modal
    $('#create_record').click(function () {
      action.openInsertionModal();
    });

    // Insert
    action.insert();

    // Delete
    window.showConfirmationModal = function showConfirmationModal(id) {
      action.delete(id);
    }

    // Edit Modal
    window.showEditModal = function showEditModal(id) {
      action.reloadModal();

      $.ajax({
        url: "{{ url('generalInfo/edit') }}",
        method: 'get',
        data: { id: id },
        success: function (data) {  
          action.editOnSuccess(id);
          $('#bank_balance').val(data.bank_balance);
          $('#jalaliMonth').val(data.jalaliMonth).trigger('change');
          $('#jalaliYear').val(data.jalaliYear).trigger('change');
        }
      })
    }
  });
</script>
@endsection