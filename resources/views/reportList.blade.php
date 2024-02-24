@extends('layouts.admin')
@section('title', 'لیست ')

@section('content')

  {{-- Header --}}
  <x-header pageName="گزارش" buttonValue="گزارش">
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
        <x-input type="number" key="range" placeholder="ردیف های مرتبط"
          class="col-md-6 mb-3" />

        <div class="col-md-12 mb-3">
          <!-- Type -->
          <label for="type">نوع گزارش:</label>
          <select name="type" id="type">
              <option value="1">هزینه حقوق کارمندان</option>
              <option value="2">هزینه آموزش</option>
              <option value="3">هزینه های سلامت</option>
          </select>
        </div>

        {{-- Description --}}
        <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-2" />
      </div>

      <!-- File -->
      <h6>ارسال رسید</h6>
      <input type="file" id="file" name="receipt" class="mb-3" 
        accept="application/pdf,image/*,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>

    </x-slot>
  </x-admin.insert>

  
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

    // Record modal
    $('#printButton').click(function () {
      window.location.href = "/print";
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
        url: "{{ url('report/edit') }}",
        method: 'get',
        data: { id: $url },
        success: function (data) {  
          action.editOnSuccess($url);
          $('#jalaliMonth').val(data.jalaliMonth).trigger('change');
          $('#jalaliYear').val(data.jalaliYear).trigger('change');
          $('#type').val(data.type).trigger('change');
          $('#expenses').val(data.expenses);
          $('#range').val(data.range);
          $('#description').val(data.description);
        }
      })
    }
  });
</script>
@endsection
