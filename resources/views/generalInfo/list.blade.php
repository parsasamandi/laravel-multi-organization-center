@extends('layouts.admin')
@section('title', 'فهرست گزارش های کالی')

@section('content')

  {{-- Header --}}
  <x-header pageName="گزارشات کلی" pageDescription="گزارشات موجودی و فرستادن صورت حساب" buttonValue="گزارش کلی جدید">
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

        <x-input type="number" key="bank_balance" placeholder="موجودی در پایان ماه (ریال)" 
          class="col-md-12 mb-3"/>

      </div>
      <!-- File -->
      <h6>ارسال چاپ صورت حساب بانکی</h6>
      <input type="file" id="file" name="receipt" class="mb-3" 
        accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,application/csv"/>

    </x-slot>
  </x-admin.insert>


  {{-- Delete --}}
  <x-admin.delete title="'گزارش'" />

  
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
      action.redirectPage('/generalInfo/edit/' + url);
    }
  });
</script>
@endsection
