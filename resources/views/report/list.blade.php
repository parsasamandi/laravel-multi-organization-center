@extends('layouts.admin')
@section('title', 'فهرست گزارش جزئیات هزینه')

@section('content')

  @include('includes.successModal')

  {{-- Header --}}
  <x-header pageName="هزینه ها" 
    buttonValue="گزارش هزینه">
    <x-slot name="table">
      <x-table :table="$reportTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-lg" formId="reportForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <!-- Date -->
        <div class="col-md-6 mb-3">
          @include('includes.jalaliYearsSelectBox')
        </div>

        <div class="col-md-6 mb-3">
          @include('includes.jalaliMonthsSelectBox')
        </div>

        <!-- Expenses -->
        <x-input key="expenses" placeholder="مبلغ هزینه (ریال)"
          class="col-md-6 mb-3" />

        <!-- Type -->
        <div class="col-md-6 mb-3">
          <!-- Type -->
          <label for="type">نوع هزینه:</label>
          <select id="type" name="type">
              <option value="0">هزینه حقوق کارمندان</option>
              <option value="1">هزینه آموزش</option>
              <option value="2">هزینه های سلامت</option>
          </select>
        </div>

        <!-- Range -->
        <x-input key="range" placeholder="ردیف های هزینه در صورتحساب بانکی (لطفا با ویرگول جدا گردد)"
          class="col-md-12 mb-3" />


        {{-- Description --}}
        <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-2" />
      </div>

      <!-- File -->
      <h6>پیوست نمودن فایل فاکتور</h6>
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
    window.showConfirmationModal = function showConfirmationModal(url) {
      action.delete(url);
    }

    // Edit
    window.showEditModal = function showEditModal(url) {
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('report/edit') }}",
        method: 'get',
        data: { id: url },
        success: function (data) {  
          console.log(data);
          action.editOnSuccess(url);
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
