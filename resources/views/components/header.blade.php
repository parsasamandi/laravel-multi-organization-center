<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <h2 class="mt-4">فهرست {{ $pageName }}</h2>
    <ol class="breadcrumb mb-4 right-text">
        <li class="breadcrumb-item">صفحه {{ $pageName }}</li>
    </ol>

    {{-- Button --}}
    <button type="button" id="create_record"
        class="btn btn-primary btn-sm">افزودن {{ $buttonValue }}</button>

    <hr>
    
    {{-- Responsive Table --}}
    <div class="table-responsive">
        {{ $table ?? null }}
    </div>
</div>


