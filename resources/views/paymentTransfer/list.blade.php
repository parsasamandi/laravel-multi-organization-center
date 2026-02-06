@extends('layouts.admin')
@section('title', 'List Of Payment Information')

@section('content')

  @include('includes.successModal', ['english' => true])

  {{-- Header --}}
  <x-header pageName="اطلاعات پرداخت‌‌ها" buttonValue="اطلاعات پرداخت جدید" :type="1">
    <x-slot name="table">
      <x-table :table="$paymentTransferTable" />
    </x-slot>
  </x-header>

  {{-- Insertion --}}
  <x-admin.insert size="modal-l" formId="paymentTransferForm" english="true">
    <x-slot name="content">
      {{-- Form --}}  
      <div class="row">
        <!-- Transferred Date -->
        <x-input key="date" placeholder="Date (YYYY-MM-DD)"
          class="col-md-6 mb-2" />

        <!-- Centers -->
        <div class="col-md-6 mb-3">
          <label for="center_id">Center:</label>
          <select id="center_id" name="center_id" class="form-control">
            @foreach($centers as $center)
              <option value="{{ $center->id }}">{{ $center->name_en }}</option>
            @endforeach
          </select>
        </div> 

        <!-- Total Payment (CAD) -->
        <x-input key="total_cad" placeholder="Total Payment (CAD)"
          class="col-md-6 mb-3" />

        <!-- Total Payment (RIAL) -->
        <x-input key="total_rial" placeholder="Total Payment (RIAL)"
          class="col-md-6 mb-3" />

        <!-- CAD to USD rate -->
        <x-input key="cad_to_usd_rate" placeholder="CAD To USD Rate"
          class="col-md-6 mb-3" />
      </div>

      <!-- The rest of the form -->
      <div class="row">
        <!-- Salary Payment (CAD) -->
        <x-input key="salary" placeholder="Salary Payment (CAD)"
          class="col-md-6 mb-3" />

        <!-- Education Payment (CAD) -->
        <x-input key="education" placeholder="Education Payment (CAD)"
          class="col-md-6 mb-3" />

        <!-- Food Payment (CAD) -->
        <x-input key="food" placeholder="Food Payment  (CAD)"
          class="col-md-6 mb-3" />

        <!-- Outfit Payment (CAD) -->
        <x-input key="outfit" placeholder="Outfit Payment (CAD)"
          class="col-md-6 mb-3" />

        <!-- Miscellaneous Payment (CAD) -->
        <x-input key="misc" placeholder="Misc Payment (CAD)"
          class="col-md-6 mb-3" />

        <!-- The description for miscellaneous payments -->
        <x-textarea key="misc_desc" rows="6" placeholder="Desc For Misc Payments" class="col-md-6" />
      </div>
    </x-slot>
  </x-admin.insert>

  {{-- Delete --}}
  <x-admin.delete title="this Payment transfer" english="true" />

@endsection

@section('scripts')
@parent

<!-- Payment Transfer Data Table -->
{!! $paymentTransferTable->scripts() !!}

<script>
  $(document).ready(function () {
    
    // Expense Transfer Table
    let dt = window.LaravelDataTables['paymentTransferTable'];
    let action = new RequestHandler(dt,'#paymentTransferForm', 'paymentTransfer', "English");

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

    // Edit
    window.showEditModal = function showEditModal(id) {
      action.reloadModal();

      $.ajax({
    url: "{{ url('paymentTransfer/edit') }}",
    method: 'get',
    data: { id: id },
    success: function (data) {  
      action.editOnSuccess(id);

        console.log("Converted Gregorian date:", data.date);  // Should now be a valid Gregorian date
        
        // Set the Gregorian date in the input field
        $('#date').val(data.date);

        // Populate other fields
        $('#cad_to_usd_rate').val(data.cad_to_usd_rate);
        $('#total_rial').val(data.total_rial);
        $('#total_cad').val(data.total_cad);
        $('#operation').val(data.operation);
        $('#outfit').val(data.outfit);
        $('#education').val(data.education);
        $('#salary').val(data.salary);
        $('#food').val(data.food);
        $('#misc').val(data.misc);
        $('#misc_desc').val(data.misc_desc);
        $('#center_id').val(data.center_id).trigger('change');
    }
});
    }
  });
</script>
@endsection