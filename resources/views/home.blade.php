@extends('layouts.admin')
@section('title','اتوماسیون گزارش هزینه های ماهانه')

@section('content')
    <!-- <div class="container-fluid">
        <div class="x_panel mt-3">
            <h4 class="bg-primary admin_panel">
                به اتوماسیون گزارش هزینه های ماهانه گلستان خوش آمدید.
                 لطفا برای مشاهده گزینه ها، منوی سمت راست را باز کنید.
        </h4>
        </div>
    </div> -->
    <!-- Main content -->
    <!-- <div class="content mt-4"> -->
                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">به اتوماسیون گزارش هزینه های ماهانه گلستان خوش آمدید.</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        مرکز محترم، شما در حال حاضر در صفحه اصلی هستید.
                                        برای دسترسی به انتخاب ها، میتوانید منوی سمت راست را باز کنید،
                                         یا از دسترسی سریع زیر برای انتقال به صفحه مورد نظر خود استفاده کنید. لطفا در صورت بروز هر مشکلی، با پشتیبانی در تماس باشید.                                    </p>

                                    <a href="{{ url('center/list') }}" class="btn btn-primary mt-2">انتقال به صفحه اطلاعات مرکز</a>
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
            <!-- </div> -->
@endsection



