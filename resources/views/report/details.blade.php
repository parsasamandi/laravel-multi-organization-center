@extends('layouts.admin')
@section('title', 'جزئیات گزارش هزینه')

@section('content')

@php
    // $convertor is an instance of the Action class
    $convertor = new \App\Providers\Convertor();
    
    // Jalali month
    $jalaliMonth = $convertor->convertJalaliMonth($report->generalInfo->jalaliMonth);
    // Jalali year
    $jalaliYear = $convertor->englishToPersianDecimal($report->generalInfo->jalaliYear);
    // Expenses
    $expenses = $convertor->englishToPersianDecimal($report->expenses);
    // Range
    $range = $convertor->englishToPersianDecimal($report->range);
    // Presigned URL
    $presignedUrl = $convertor->getPresignedUrlWithContentDisposition('receipt/' 
        . $report->receipt, $report->receipt);
@endphp


<div class="container-fluid right-text">
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
            <td>{{ $report->center->name }}</td>
            <!-- Expenses -->
            <td>{{ $expenses }}</td>
            <!-- Range -->
            <td>{{ $range }}</td>
            <!-- Type -->
            @switch($report->type)
                @case(0)
                    <td>هزینه حقوق کارمند</td>
                    @break
                @case(1)
                    <td>هزینه آموزش</td>
                    @break
                @case(2)
                    <td>هزینه های سلامت</td>
                    @break
                @case(3)
                    <td>هزینه های غذا</td>
                    @break
                @case(4)
                    <td>هزینه های پوشاک</td>
                    @break
                @case(5)
                    <td>هزینه های دیگر</td>
                    @break
            @endswitch
            <!-- Jalali months -->
            <td>{{ $jalaliMonth }}</td>
            <!-- Jalali Year -->
            <td>{{ $jalaliYear }}</td>
            <!-- Receipt -->
            <td><a href="{{ $presignedUrl }}" target="_blank">دانلود</a></td>
        </x-slot>
    </x-details>    

    <!-- Description -->
    <x-textarea key="description" class="mt-2" rows="2" 
        placeholder="توضیحات" value="{{ $report->description }}" readonly />

    <!-- Status form -->
    @include('includes.form.statusForm', ['id' => $report->id, 'status' => $report->statuses->status])

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

        // Print button
        $('#print_button').click(function() {
            window.print();
        });

    </script>

@endsection