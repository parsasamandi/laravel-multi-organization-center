@extends('layouts.admin')
@section('title', 'جزئیات گزارش هزینه')

@section('content')

@php
    // $action is an instance of the Action class
    $convertor = new \App\Providers\Convertor();

    $jalaliMonth = $convertor->numberTojalaliMonthBlade($report->generalInfo->jalaliMonth);
    $jalaliYear = $convertor->englishToPersianDecimal($report->generalInfo->jalaliYear);
    $expenses = $convertor->englishToPersianDecimal($report->expenses);
    $range = $convertor->englishToPersianDecimal($report->range);

@endphp


<div class="container-fluid mt-3">
    {{-- List --}}
    <x-details tableId="reportDetailsTable" header="گزارش هزینه {{ $jalaliMonth }} {{ $jalaliYear }}">

        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>مرکز</th>
            <th>مبلغ هزینه</th>
            <th>ردیف ها در صورتحساب بانکی</th>
            <th>نوع هزینه</th>
            <th>ماه</th>
            <th>سال</th>
            <th>دانلود رسید</th>
        </x-slot>       

        <!-- Table data -->
        <x-slot name="tableData">
            <!-- Center name -->
            <td>{{ Auth::user()->name }}</td>
            <!-- Expenses -->
            <td>{{ $expenses }}</td>
            <!-- Range -->
            <td>{{ $range }}</td>

            <!-- Type -->
            @switch($report->type)
                @case(0)
                    <td>حقوق کارمند</td>
                    @break
                @case(1)
                    <td>هزینه آموزش</td>
                    @break
                @case(2)
                    <td>هزینه های سلامت</td>
                    @break
            @endswitch

            <!-- Jalali months -->
            <td>{{ $jalaliMonth }}</td>

            <!-- Jalali Year -->
            <td>{{ $jalaliYear }}</td>

            <!-- Receipt -->
            <td>
                <a href="{{ Storage::disk('s3')->temporaryUrl('receipts/' . 
                        $report->receipt, now()->addHours(1)) }}" download>دانلود</a>
            </td>
        </x-slot>

    </x-details>    

    <!-- Description -->
    <x-textarea key="description" class="mt-2" rows="2" placeholder="توضیحات" value="{{ $report->description }}" readonly />

    <!-- Status form -->
    @include('includes.form.status', ['id' => $report->id])

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
            $('#reportDetailsTable').DataTable({
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
        });

         // Form submission for updating
         $('#confirmStatus').click(function (event) {
            event.preventDefault();

            // AJAX request
            $.ajax({
                url: '/report/confirmStatus',
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                data: $('#statusForm').serialize(),
                success: function(response) {
                    // Handle success response
                    window.location.href = '/report/list';      
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    error(error);      
                }
            });
        });

        $('#print_button').click(function() {
            var $tableToPrint = $('#reportDetailsTable').clone(); // Clone the table
            $tableToPrint.find('button').remove(); // Remove any buttons in the cloned table
            
            var $printWindow = window.open('', '_blank'); // Open a new window
            $printWindow.document.body.innerHTML = '<table class="table table-bordered">' + $tableToPrint.html() + '</table>'; // Append the table to the new window
            
            $printWindow.print(); // Print the window
            $printWindow.close(); // Close the window after printing
        });
    </script>

@endsection