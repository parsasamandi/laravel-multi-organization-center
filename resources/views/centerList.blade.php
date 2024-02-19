@extends('layouts.admin')
@section('title', 'لیست مرکز')

@section('content')

  {{-- Header --}}
  <x-header pageName="لیست مرکز" buttonValue="لیست مرکز">
    <x-slot name="table">
      <x-table :table="$centerTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="centerForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <input type="file" id="file" name="file" 
          accept="application/pdf,image/*,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
      </div>
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="مرکز" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $centerTable->scripts() !!}

<script>
  $(document).ready(function () {
    // Center Table
    let dt = window.LaravelDataTables['centerTable'];
    let action = new RequestHandler(dt,'centerForm', 'center');

    // Record modal
    $('#create_record').click(function () {
      action.modal();
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
      action.edit();

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
