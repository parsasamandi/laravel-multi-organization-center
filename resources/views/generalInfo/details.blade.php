@extends('layouts.admin')
@section('title', 'جزئیات')

@section('content')

<div class="container-fluid mt-3 right-text">
    <x-details tableId="reportDetails">
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
            <td><a href="{{ url('receipts/' . $generalInfo->bank_statement_receipt) }}" download>دانلود  
                {{ $generalInfo->bank_statement_receipt }} </a></td>
        </x-slot>
        
    </x-details>

</div>

@endsection


