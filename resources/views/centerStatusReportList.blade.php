@extends('layouts.admin')
@section('title', 'فهرست اطلاعات مرکز')

@section('content')
  {{-- Header --}}
  <x-header pageName="وضعیت ماهانه مراکز" buttonValue="null" :type="1">
    <x-slot name="table">
      <x-table :table="$centerStatusReportTable" />
    </x-slot>
  </x-header>

  {{-- Delete --}}
  <x-admin.delete title="مرکز" english="false" />
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $centerStatusReportTable->scripts() !!}


<script>
  $(document).ready(function () {

    // Change the English number of pagination to Persian
    $('#centerStatusReportTable').DataTable().on('draw', function() {
      convertNumbersToPersian('.dataTables_paginate .paginate_button');
    });

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

  });
</script>
@endsection