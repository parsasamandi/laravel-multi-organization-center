@extends('layouts.admin')
@section('title', 'جزئیات گزارش یک ردیف هزینه‌کرد')

@section('content')

<!-- <div class="container-fluid mt-3 right-text"> -->
<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <x-details tableId="reportDetailsTable" header="گزارش یک ردیف هزینه‌کرد">

        <!-- Table header -->
        <x-slot name="tableHeader">
            <th>مبلغ هزینه</th>
            <th>ردیف ها در صورتحساب بانکی</th>
            <th>نوع گزارش</th>
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
            <td>{{ $report->generalInfo->jalaliMonth }}</td>

            <!-- Jalali Year -->
            <td>{{ $report->generalInfo->jalaliYear }}</td>

            <!-- Receipt -->
            <td><a href="{{ url('/receipts/' . $report->receipt) }}" download>دانلود  
                    {{ $report->receipt }} </a></td>
        </x-slot>

    </x-details>

    <!-- Description -->
    <x-textarea key="description" class="mt-2" rows="2" placeholder="توضیحات" value="{{ $report->description }}" readonly />

    <!-- Status form -->
    @include('includes.form.status', ['id' => $report->id])

    <!-- Return button -->
    <div class="text-center mt-3">
        <button type="button" id="return_button" class="btn btn-secondary">بازگشت</button>
        <button type="button" id="print_button" class="btn btn-secondary">چاپ</button>
        <button id="exportExcelButton" class="btn btn-secondary">خروجی اکسل</button>
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

            // Get form data
            var formData = $('#statusForm').serialize(); 

            // AJAX request
            $.ajax({
                url: '/report/confirmStatus',
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                data: formData,
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

        // Export button
        $('#exportExcelButton').click(function() {
            // Get the table data as a worksheet
            var worksheet = XLSX.utils.table_to_sheet(document.getElementById('reportDetailsTable'));

            // Create a workbook and add the worksheet to it
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'جزئیات یک ردیف هزینه‌کرد');

            // Convert the workbook to an Excel file (binary string)
            var excelBinaryString = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

            // Convert the binary string to a Blob
            var blob = new Blob([s2ab(excelBinaryString)], { type: 'application/octet-stream' });

            // Create a temporary anchor element
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'جزئیات-هزینه‌کرد.xlsx'; // Set the filename for the downloaded file

            // Append the anchor element to the document body and trigger a click event to start the download
            document.body.appendChild(a);
            a.click();

            // Remove the anchor element from the document body
            document.body.removeChild(a);
        });

        // Function to convert string to ArrayBuffer
        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

    </script>

@endsection




