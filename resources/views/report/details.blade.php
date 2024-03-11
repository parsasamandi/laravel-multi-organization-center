@extends('layouts.admin')
@section('title', 'جزئیات گزارش جزئی')

@section('content')

<!-- <div class="container-fluid mt-3 right-text"> -->
<div id="content-to-print">
    {{-- List --}}
    <x-details tableId="reportDetailsTable" header="گزارش جزئی">

        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>مبلغ هزینه</th>
            <th>ردیف ها در صورتحساب بانکی</th>
            <th>نوع</th>
            <th>ماه</th>
            <th>سال</th>
            <th>دانلود رسید</th>
        </x-slot>       

        <!-- Table data -->
        <x-slot name="tableData">
            <!-- Expenses -->
            <td>{{ $report->expenses }}</td>
            <!-- Range -->
            <td>{{ $report->range }}</td>
            <!-- Type -->
            @switch($report->type)
                @case(0)
                    <td>گزارش حقوق کارمند</td>
                    @break
                @case(1)
                    <td>گزارش هزینه آموزش</td>
                    @break
                @case(2)
                    <td>گزارش هزینه های سلامت</td>
                    @break
            @endswitch

            <!-- Jalali months -->
            @php
                $months = [
                    1 => 'فروردین',
                    2 => 'اردیبهشت',
                    3 => 'خرداد',
                    4 => 'تیر',
                    5 => 'مرداد',
                    6 => 'شهریور',
                    7 => 'مهر',
                    8 => 'آبان',
                    9 => 'آذر',
                    10 => 'دی',
                    11 => 'بهمن',
                    12 => 'اسفند',
                ];
            @endphp

            <td>{{ $months[$report->generalInfo->jalaliMonth] }}</td>

            <!-- Jalali Year -->
            <td>{{ $report->generalInfo->jalaliYear }}</td>
            <!-- Receipt -->
            <td><a href="{{ url('/receipts/' . $report->receipt) }}" download>دانلود  
                    {{ $report->receipt }} </a></td>
        </x-slot>

    </x-details>

    <!-- Description -->
    <x-textarea key="description" class="col-md-12" rows="2" placeholder="توضیحات" value="{{ $report->description }}" readonly />


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
        $(document).ready(function() {
            $('#reportDetailsTable').DataTable({
                searching: false,
                lengthMenu: [], // Remove display length feature
                info: false,
                ordering: true,
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




