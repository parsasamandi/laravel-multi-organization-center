<!-- Details -->

<div class="table-responsive">
    <!-- Header -->
    <h2 class="mt-2 mb-3">جزئیات {{ $header ?? null }}</h2>

    <div class="row">
        <div class="col-md-12">
            <table id="{{ $tableId ?? null }}" class="table table-bordered table-striped w-100 
                                                        nowrap text-center dataTable no-footer dtr-inline collapsed">
                <thead>
                    <tr>
                        {!! $tableHeader ?? null !!} 
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {!! $tableData ?? null !!} 
                    </tr>
                </tbody>
            </table>
        </div>
    </div>  
</div>

