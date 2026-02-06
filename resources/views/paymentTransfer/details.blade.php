@extends('layouts.admin')
@section('title', 'جزئیات گزارش هزینه')

@section('content')

@php
  // $convertor is an instance of the Action class
  $convertor = new \App\Providers\Convertor();

  $rialToCADRate = $paymentTransfer->total_rial / $paymentTransfer->total_cad;
@endphp


<div class="container-fluid mt-3 text-left">

    <h4 class="mb-3 mt-3">Details for {{ $paymentTransfer->date }}</h4>
    <hr>

    <div class="row ">
      <!-- Date -->
      <x-input key="date" placeholder="Date (YYYY/MM/DD)"
        class="col-md-3 mb-2" value="{{ $paymentTransfer->date }}" readonly />

      <!-- Center -->
      <x-input key="center_name" placeholder="Center Name" 
        class="col-md-3 mb-3" value="{{ $paymentTransfer->center->name_en }}" readonly />

      <!-- Total Payment (CAD) -->
      <x-input key="total_cad" placeholder="null"
        class="col-md-3 mb-3" value="{{ $paymentTransfer->total_cad }}" readonly />

      <!-- Total Payment (RIAL) -->
      <x-input key="total_rial" placeholder="Total Payments (RIAL)"
        class="col-md-3 mb-3" value="{{ $paymentTransfer->total_rial }}" readonly />

      <!-- CAD To USD rate -->
      <x-input key="cad_to_usd_rate" placeholder="Conversion Rate (CAD → USD)"
        class="col-md-3 mb-3" value="{{ $paymentTransfer->cad_to_usd_rate }}" readonly />
      
      <!-- Rial To CAD rate -->
      <x-input key="rial_to_cad_rate" placeholder="Conversion Rate (Rial → CAD)"
        class="col-md-3 mb-3" value="{{ $rialToCADRate }}" readonly />
      
      <!-- Salary Payment (CAD) -->
      <x-input key="rial_to_cad_rate" placeholder="Salary Payment (CAD)"
        class="col-md-3 mb-3" value="{{ $paymentTransfer->salary }}" readonly />

      <!-- Education Payment (CAD) -->
      <x-input key="education" placeholder="Education Payment (CAD)"
        class="col-md-3 mb-3" value="{{ $paymentTransfer->education }}" readonly />

      <!-- Food Payment (CAD) -->
      <x-input key="food" placeholder="Food Payment (CAD)"
        class="col-md-4 mb-3" value="{{ $paymentTransfer->food }}" readonly />

      <!-- Outfit Payment (CAD) -->
      <x-input key="outfit" placeholder="Outfit Payment (CAD)"
        class="col-md-4 mb-3" value="{{ $paymentTransfer->outfit }}" readonly />

      <!-- Miscellaneous Payment (CAD) -->
      <x-input key="misc" placeholder="Miscellaneous Payment (CAD)"
        class="col-md-4 mb-3" value="{{ $paymentTransfer->misc }}" readonly />

      <!-- The description for miscellaneous expenses -->
      <x-textarea key="misc_desc" placeholder="Description For Miscellaneous Payments" class="col-md-12" value="{{ $paymentTransfer->misc_desc }}" readonly />
    </div>

    <!-- Return button -->
    <div class="text-center mt-3">
      <button type="button" id="print_button" class="btn btn-primary">
          {{ $english ? 'Print' : 'چاپ' }}
      </button>
      <button type="button" id="return_button" class="btn btn-secondary">
          {{ $english ? 'Return' : 'بازگشت' }}
      </button>
    </div>

</div>

@endsection

@section('scripts')
@parent
  <script>
    // Print button
    $('#print_button').click(function() {
      window.print();
    });
  </script>
@endsection