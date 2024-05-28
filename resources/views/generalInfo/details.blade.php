@extends('layouts.admin')
@section('title', 'جزئیات یک ردیف از گزارش کلی')

@section('content')

@php
    // $action is an instance of the Action class
    $action = new \App\Providers\Action();
@endphp

<div class="container-fluid mt-3 right-text">

    <x-details tableId="generalInfoDetailsTable" header="یک ردیف از صورتحساب">
        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>سال</th>
            <th>ماه</th>
            <th>موجودی در پایان ماه</th>
            <th>دانلود صورتحساب بانکی</th>
        </x-slot>

        <!-- Table data -->
        <x-slot name="tableData">
            <td>{{ $action->englishToPersianNumbers($generalInfo->jalaliYear) }}</td>
             <!-- Jalali months -->
            <td>{{ $action->jalaliMonth($generalInfo->jalaliMonth) }}</td>
            <td>{{ $action->englishToPersianNumbers($generalInfo->bank_balance) }}</td>
            <td>
                <a href="{{ Storage::disk('s3')->temporaryUrl('receipts/' . 
                        $generalInfo->bank_statement_receipt, now()->addHours(1)) }}" download>دانلود</a>
            </td>


        </x-slot>
        
    </x-details>

    <!-- Status form -->
    @include('includes.form.status', ['id' => $generalInfo->id])

    <!-- Return button -->
    <div class="text-center mt-3">
        <button type="button" id="print_button" class="btn btn-primary">چاپ</button>
        <button type="button" id="return_button" class="btn btn-secondary">بازگشت</button>
    </div>

</div>

@endsection

@section('scripts')
@parent
    <script>
        $(document).ready(function() {

            // Status
            $('#status').val({{ json_encode($generalInfo->statuses->status) }}).trigger('change');

            $('#generalInfoDetailsTable').DataTable({
                searching: false,
                lengthMenu: [], // Remove display length feature
                info: false,
                ordering: false,
                responsive: true,
                pageLength: 10,
                dom: 'frti', // Remove display length feature
                order: [[0, 'asc']], // Change column index and order direction as needed
                language: {
                    url: "{{ asset('js/persian.json') }}",
                    lengthMenu: '' // Remove "Show 10 entries" text
                }
            });

            // Form submission for updating
            $('#confirmStatus').click(function (event) {
                event.preventDefault();

                // Get form data
                var formData = $('#statusForm').serialize(); 

                // AJAX request
                $.ajax({
                    url: '/generalInfo/confirmStatus',
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        window.location.href = '/generalInfo/list';      
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        error(error);      
                    }
                });
            });
        });

        // Print button
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