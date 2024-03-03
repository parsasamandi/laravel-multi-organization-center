@extends('layouts.admin')
@section('title', 'جزئیات گزارش جزئی')

@section('content')

<div class="container-fluid mt-3 right-text">
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
                @default
                    <td>Unknown Report Type</td>
            @endswitch

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
            <td><a href="{{ url('receipts/' . $report->receipt) }}" download>دانلود  
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
        $('#print_button').click(function() {
            window.print();
        });
    </script>






@endsection




