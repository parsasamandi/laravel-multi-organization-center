@extends('layouts.admin')
@section('title','لیست شماره تلفن ها')

@section('content')

  {{-- Header --}}
  <x-header pageName="شماره تلفن" buttonValue="شماره تلفن">
    <x-slot name="table">
      <x-table :table="$phoneNumberTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="phoneForm">
    <x-slot name="content">
      {{-- Form --}}
      <div class="row">
        <x-input key="number" placeholder="تلفن همراه" 
          class="col-md-12 mb-3" />
          
        {{-- Product select box --}}
        <div class="col-md-12">
          @include('includes.form.productSelectBox')
        </div>
      </div>
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="شماره تلفن" />
  
@endsection

@section('scripts')
@parent

<!-- DataTable data -->
{!! $phoneNumberTable->scripts() !!}

<script>
  $(document).ready(function () {
    // phoneNumber Table
    let dt = window.LaravelDataTables['phoneNumberTable'];
    let action = new RequestHandler(dt,'phoneForm', 'phoneNumber');

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
        url: "{{ url('phoneNumber/edit') }}",
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
