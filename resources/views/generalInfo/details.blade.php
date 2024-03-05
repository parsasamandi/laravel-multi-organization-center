@extends('layouts.admin')
@section('title', 'جزئیات')

@section('content')

<div class="container-fluid mt-3 right-text">

    <x-details tableId="generalInfoDetailsTable">
        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>سال</th>
            <th>ماه</th>
            <th>موجودی در پایان ماه</th>
            <th>دانلود صورت حساب بانکی</th>
        </x-slot>

        <!-- Table data -->
        <x-slot name="tableData">
            <td>{{ $generalInfo->jalaliYear }}</td>
            <td>{{ $generalInfo->jalaliMonth }}</td>
            <td>{{ $generalInfo->bank_balance }}</td>
            <td><a href="{{ url('/receipts/' . $generalInfo->bank_statement_receipt) }}" download>دانلود  
                {{ $generalInfo->bank_statement_receipt }} </a></td>
        </x-slot>
        
    </x-details>

    <!-- Return button -->
    <div class="text-center mt-3">
        <button type="button" id="return_button" class="btn btn-secondary">بازگشت</button>
        <button type="button" id="print_button" class="btn btn-secondary">چاپ</button>
    </div>

</div>

@endsection

@section('scripts')
@parent
    <script>
        $('#print_button').click(function() {
            var $tableToPrint = $('#generalInfoDetailsTable').clone(); // Clone the table
            $tableToPrint.find('button').remove(); // Remove any buttons in the cloned table
            
            var $printWindow = window.open('', '_blank'); // Open a new window
            $printWindow.document.body.innerHTML = '<table class="table table-bordered">' + $tableToPrint.html() + '</table>'; // Append the table to the new window
            
            $printWindow.print(); // Print the window
            $printWindow.close(); // Close the window after printing
        });
    </script>
@endsection


