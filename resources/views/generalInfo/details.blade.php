@extends('layouts.admin')
@section('title', 'جزئیات یک ردیف از گزارش کلی')

@section('content')

<div class="container-fluid mt-3 right-text">

    <x-details tableId="generalInfoDetailsTable" header="یک ردیف از گزارش کلی">
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
             <!-- Jalali months -->
            <td>{{ $generalInfo->jalaliMonth }}</td>
            <td>{{ $generalInfo->bank_balance }}</td>
            <td><a href="{{ url('/receipts/' . $generalInfo->bank_statement_receipt) }}" download>دانلود  
                {{ $generalInfo->bank_statement_receipt }} </a></td>
        </x-slot>
        
    </x-details>

    @if(Auth::user()->type == 1)
        <form id="statusForm">
            {{ csrf_field() }}

            {{-- Output --}}
            <span id="form_output"></span>

            <!-- Id -->
            <input type="hidden" name="id" id="id" value="{{ $generalInfo->id }}"  />

            <div class="row">
                <!-- Confirmed or Not confirmed status -->
                @include('includes.confirmation')
            </div>
            
            <div class="col-md-12">   
                <button type="button" type="submit" id="submit" class="btn btn-secondary">ثبت وضعیت</button>
            </div>

        </form>
    @endif

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

            // Status
            $('#status').val({{ json_encode($generalInfo->statuses->status) }}).trigger('change');

            $('#generalInfoDetailsTable').DataTable({
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

             // Form submission for updating
             $('#submit').click(function () {
                event.preventDefault();

                // AJAX request
                $.ajax({
                    url: '/generalInfo/update',
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    data: {
                        id: $('#id').val(),
                        status: $('#status').val(),
                        button_action: $('#button_action').val()
                    },
                    success: function(response) {
                        console.log('test');
                        // Redirecting to the main page
                        window.location.href = "/generalInfo/list";
                    }, error: function(response) {
                        console.log(response.responseText);
                    },
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

         // Export button
         $('#exportExcelButton').click(function() {
            // Get the table data as a worksheet
            var worksheet = XLSX.utils.table_to_sheet(document.getElementById('generalInfoDetailsTable'));

            // Create a workbook and add the worksheet to it
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'یک ردیف از اطلاعات کلی');

            // Convert the workbook to an Excel file (binary string)
            var excelBinaryString = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

            // Convert the binary string to a Blob
            var blob = new Blob([s2ab(excelBinaryString)], { type: 'application/octet-stream' });

            // Create a temporary anchor element
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'جزئیات-اطلاعات-کلی.xlsx'; // Set the filename for the downloaded file

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


