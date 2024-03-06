@extends('layouts.admin')
@section('title', 'فهرست گزارش جزئی')

@section('content')

  {{-- Header --}}
  <x-header pageName="گزارشات جزئیات هزینه کرد" pageDescription="گزارشات جزئیات هزینه‌کرد" buttonValue="گزارش جزئی هزینه کرد">
    <x-slot name="table">
      <x-table :table="$reportTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="reportForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">

        <!-- Jalali Years -->
        <div class="col-md-6 mb-3">
          @include('includes.jalaliYearsSelectBox')
        </div>

        <!-- Jalali Months -->
        <div class="col-md-6">
          @include('includes.jalaliMonthsSelectBox')
        </div>

        <!-- Expenses -->
        <x-input type="number" key="expenses" placeholder="مبلغ هزینه"
          class="col-md-6 mb-3" />
        <!-- Range -->
        <x-input type="number" key="range" placeholder="ردیف هزینه"
          class="col-md-6 mb-3" />

        <div class="col-md-12 mb-3">
          <!-- Type -->
          <label for="type">نوع گزارش:</label>
          <select name="type" id="type">
              <option value="0">هزینه حقوق کارمندان</option>
              <option value="1">هزینه آموزش</option>
              <option value="2">هزینه های سلامت</option>
          </select>
        </div>

        {{-- Description --}}
        <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-2" />
      </div>

      <!-- File -->
      <h6>ارسال رسید</h6>
      <input type="file" id="file" name="receipt" class="mb-3" 
        accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,application/csv"/>

    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="گزارش جزئی" />

  
@endsection

@section('scripts')
@parent

<!-- Report Data Table -->
{!! $reportTable->scripts() !!}


<script>
  $(document).ready(function () {

    // Report Table
    let dt = window.LaravelDataTables['reportTable'];
    let action = new RequestHandler(dt,'#reportForm', 'report');

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
      action.redirectPage('/report/edit/' + url);
    }
    
  });
</script>
@endsection
