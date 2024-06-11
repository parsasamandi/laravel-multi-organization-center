@extends('layouts.admin')
@section('title','اتوماسیون گزارش هزینه های ماهانه')

@section('content')
    <!-- Main content -->
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">به اتوماسیون گزارش هزینه های ماهانه گلستان خوش آمدید.</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            @if(Auth::user()->type == 1)
                                عضو محترم گلستان، 

                            @elseif(Auth::user()->type == 0)
                                مرکز محترم، 
                            @endif
                             شما در حال حاضر در صفحه اصلی هستید.
                            برای دسترسی به انتخاب ها، میتوانید منوی سمت راست را باز کنید،
                                یا از دسترسی سریع زیر برای انتقال به صفحه مورد نظر خود استفاده کنید. لطفا در صورت بروز هر مشکلی، با پشتیبانی در تماس باشید.
                        </p>
                        @if(Auth::user()->type == 1)
                            <a href="{{ url('center/list') }}" class="btn btn-primary mt-2">انتقال به صفحه اطلاعات مرکز</a>
                            <a href="{{ url('golestanTeam/list') }}" class="btn btn-primary mt-2">انتقال به صفحه تیم گلستان</a>
                        @endif
                        <a href="{{ url('generalInfo/list') }}" class="btn btn-primary mt-2">انتقال به صفحه گزارش صورتحساب بانکی</a>
                        <a href="{{ url('report/list') }}" class="btn btn-primary mt-2">انتقال به صفحه گزارش هزینه‌ها</a>
                    </div>
                </div>
                <!-- /.card -->
            </div>
    
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
@endsection



