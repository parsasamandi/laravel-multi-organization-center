@extends('layouts.admin')
@section('title', 'لیست گزارش جزئیات هزینه')

@section('content')

  @include('includes.successModal')

  {{-- Header --}}
  <x-header pageName="هزینه‌ها" buttonValue="گزارش هزینه" :type="0">
    <x-slot name="table">
      <x-table :table="$reportTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-lg" formId="reportForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        {{-- Conditional rendering based on the user type --}}
        <!-- Date -->
        <div class="col-md-6 mb-3">
          @include('includes.jalaliYearsSelectBox')
        </div>

        <div class="col-md-6 mb-3">
          @include('includes.jalaliMonthsSelectBox')
        </div>

        <!-- Expenses -->
        <x-input key="expenses" placeholder="مبلغ هزینه (ریال)"
          class="col-md-6 mb-3" required="true" />

        <!-- Type -->
        <div class="col-md-6 mb-3">
          <!-- Type -->
          <label for="type">نوع هزینه: <span class="input-required">*</span> </label>
          <select id="type" name="type">
              <option value="0">هزینه حقوق کارمندان</option>
              <option value="1">هزینه آموزش</option>
              <option value="2">هزینه های سلامت</option>
              <option value="3">هزینه های غذا</option>
              <option value="4">هزینه های پوشاک</option>
              <option value="5">هزینه های دیگر یک</option>
              <option value="6">هزینه های دیگر دو</option>
          </select>
        </div>

        <!-- Range -->
        <x-input key="range" placeholder="ردیف های هزینه در صورتحساب بانکی (لطفا با ویرگول جدا گردد)"
          class="col-md-12 mb-3" required="true" />


        {{-- Description --}}
        <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-2" />
      </div>

      <!-- File -->
      <h6 class="required-heading">پیوست نمودن فایل فاکتور <span class="input-required">*</span></h6>
      <input type="file" id="file" name="receipt" class="mb-3"
        accept=".pdf,.doc,.docx,.csv,application/msword,application/
        vnd.openxmlformats-officedocument.wordprocessingml.document,application/
        vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>

    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="گزارش هزینه" />


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
    window.showConfirmationModal = function showConfirmationModal(id) {
      console.log(id);
      action.delete(id);
    }

    // Edit
    window.showEditModal = function showEditModal(id) {
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('report/edit') }}",
        method: 'get',
        data: { id: id },
        success: function (data) {  
          action.editOnSuccess(id);
          $('#jalaliMonth').val(data.generalInfo.jalaliMonth).trigger('change');
          $('#jalaliYear').val(data.generalInfo.jalaliYear).trigger('change');
          $('#type').val(data.report.type).trigger('change');
          $('#expenses').val(data.report.expenses);
          $('#range').val(data.report.range);
          $('#description').val(data.report.description);
        }
      })
    }

  });
</script>
@endsection