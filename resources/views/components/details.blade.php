<div class="table-responsive">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
        <h5>جزئیات {{ $header ?? null }}</h5>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <table id="{{ $tableId ?? null }}" class="table table-bordered table-striped w-100 nowrap text-center dataTable no-footer dtr-inline collapsed">
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

