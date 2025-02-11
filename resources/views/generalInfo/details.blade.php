@extends('layouts.admin')
@section('title', 'جزئیات یک ردیف از گزارش کلی')

@section('content')

@php
    // $convertor is an instance of the Convertor class
    $convertor = new \App\Providers\Convertor();
    
    // Jalali month
    $jalaliMonth = $convertor->convertJalaliMonth($generalInfo->jalaliMonth);
    // Jalali year
    $jalaliYear = $convertor->englishToPersianDecimal($generalInfo->jalaliYear);
    // Bank balance 
    $bankBalance = $convertor->englishToPersianDecimal($generalInfo->bank_balance);
    // Presigned URL
    $bankStatementPresignedUrl = $convertor->getPresignedUrlWithContentDisposition('bank_statement/' 
        . $generalInfo->bank_statement_receipt, $generalInfo->bank_statement_receipt);
@endphp

<div class="container-fluid right-text">
    <x-details tableId="generalInfoDetailsTable" header="صورتحساب {{ $jalaliMonth }} {{ $jalaliYear }}">
        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>نام مرکز</th>
            <th>سال</th>
            <th>ماه</th>
            <th>موجودی در پایان ماه</th>
            <th>دانلود صورتحساب بانکی</th>
        </x-slot>

        <!-- Table data -->
        <x-slot name="tableData">
            <td>{{ $generalInfo->center->name }}</td>
            <!-- Jalali year -->
            <td>{{ $jalaliYear }}</td>
             <!-- Jalali months -->
            <td>{{ $jalaliMonth }}</td>
            <!-- Bank balance -->
            <td>{{ $bankBalance }}</td>

            <td><a href="{{ $bankStatementPresignedUrl }}" target="_blank">دانلود</a></td>
        </x-slot>
    </x-details>

    <!-- Status form -->
    @include('includes.form.statusForm', ['id' => $generalInfo->id])

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
            window.print();
        });

    </script>
@endsection