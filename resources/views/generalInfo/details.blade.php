@extends('layouts.admin')
@section('title', 'جزئیات یک ردیف از گزارش کلی')

@section('content')

<div class="container-fluid mt-3 right-text">

    <x-details tableId="generalInfoDetailsTable" header="جزئیات یک ردیف از گزارش کلی">
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
            <td>{{ $months[$generalInfo->jalaliMonth] }}</td>
            <td>{{ $generalInfo->bank_balance }}</td>
            <td><a href="{{ url('/receipts/' . $generalInfo->bank_statement_receipt) }}" download>دانلود  
                {{ $generalInfo->bank_statement_receipt }} </a></td>
        </x-slot>
        
    </x-details>

    
    <form id="generalInfoEditForm">
        {{ csrf_field() }}

        {{-- Output --}}
        <span id="form_output"></span>

        <div class="row">
            <div class="col-md-12">
                <!-- Confirmed or Not confirmed status -->
                @if(Auth::user()->type == 1)
                    {{-- Confirmation --}}
                    @include('includes.confirmation')
                @endif
            </div>
        </div>

        {{-- Buttons --}}
        <div class="form-group" align="center">
            <input type="hidden" name="id" id="id" value="{{ $generalInfo->id }}"  />
            <input type="hidden" name="button_action" id="button_action" value="update" />
        </div>
    </form>

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
        });

        // Form submission for updating
        $('#generalInfoEditForm').on('submit', function (event) {
          event.preventDefault();

            // AJAX request
            $.ajax({
                url: '/generalInfo/update', 
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: [
                    confirmation: 
                ],
                contentType: false,
                processData: false,
                success: function(response) {
                  // Handle success response
                  success(response);
                },
                error: function (response) {
                    // Handle error response
                    // error(response);
                }
            });
        });


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


