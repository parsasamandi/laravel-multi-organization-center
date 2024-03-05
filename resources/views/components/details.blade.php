<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
        <li class="breadcrumb-item">جزئیات {{ $header ?? null }}</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <table id="{{ $tableId ?? null }}" class="table table-bordered table-striped text-center">
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

