@extends('layouts.admin')
@section('title', 'فهرست گزارش جزئیات هزینه')

@section('content')

  {{-- Header --}}
  <x-header pageName="هزینه ها" pageDescription="صفحه گزارش هزینه ها"
    buttonValue="گزارش هزینه">
    <x-slot name="table">
      <x-table :table="$reportTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="reportForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <!-- Date -->
        <div class="col-md-12 mb-3">
          <label for="date">تاریخ:</label>
          <select id="general_info_id" name="general_info_id">
              @foreach ($dates as $date)
                <option value="{{ $date->id }}">
                  {{ $date->jalaliMonth }} {{ $date->jalaliYear }}
                </option>
              @endforeach
          </select>
        </div>

        <!-- Expenses -->
        <x-input key="expenses" placeholder="مبلغ هزینه (ریال)"
          class="col-md-6 mb-3" />

        <!-- Range -->
        <x-input key="range" placeholder="ردیف های هزینه"
          class="col-md-6 mb-3" />

        <!-- Type -->
        <div class="col-md-12 mb-3">
          @include('includes.report.type')
        </div>

        {{-- Description --}}
        <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-2" />
      </div>

      <!-- File -->
      <h6>ارسال رسید</h6>
      <input type="file" id="file" name="receipt" class="mb-3"
        accept=".pdf,.doc,.docx,.csv,application/msword,application/
        vnd.openxmlformats-officedocument.wordprocessingml.document,application/
        vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>

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
      // Edit
      action.reloadModal();

      $.ajax({
        url: "{{ url('report/edit') }}",
        method: 'get',
        data: { id: url },
        success: function (data) {  
          action.editOnSuccess(url);
          $('#expenses').val(data.expenses);
          $('#range').val(data.range);
          $('#type').val(data.type).trigger('change');
          $('#description').val(data.description);
        }
      })
    }

  });
</script>
@endsection
