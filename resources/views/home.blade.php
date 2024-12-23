@extends('layouts.admin')
@section('title','اتوماسیون گزارش مالی ماهانه')

@section('content')
    <!-- Main content -->
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <object data="/images/golestan-logo-dark.svg" class="brand-image img-circle elevation-3" type="image/svg+xml"></object>
                        <h5>به اتوماسیون گزارش مالی ماهانه گلستان خوش آمدید. </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            @if(Auth::user()->type == 1)
                                عضو محترم گلستان، 
                            @elseif(Auth::user()->type == 0)
                                مرکز محترم، 
                            @endif
                            شما در حال حاضر در صفحه اصلی هستید.
                            برای دسترسی به انتخاب‌ها، می‌توانید فهرست سمت راست را باز کنید،
                                یا از دسترسی سریع زیر برای انتقال به صفحه مورد نظر خود استفاده کنید. لطفا در صورت بروز هر مشکلی، با پشتیبانی در تماس باشید.
                        </p>
                        @if(Auth::user()->type == 1)
                            <a href="{{ url('center/list') }}" class="btn btn-primary mt-2">انتقال به صفحه اطلاعات مرکز</a>
                            <a href="{{ url('golestanTeam/list') }}" class="btn btn-primary mt-2">انتقال به صفحه تیم گلستان</a>
                            <a href="{{ url('paymentTransfer/list') }}" class="btn btn-primary mt-2">انتقال به صفحه اطلاعات پرداخت‌ها</a>
                        @endif
                        <a href="{{ url('generalInfo/list') }}" class="btn btn-primary mt-2">انتقال به صفحه گزارش صورتحساب بانکی</a>
                        <a href="{{ url('report/list') }}" class="btn btn-primary mt-2">انتقال به صفحه گزارش هزینه‌ها</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection




