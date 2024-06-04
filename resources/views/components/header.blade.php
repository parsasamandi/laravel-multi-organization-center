<div class="container-fluid mt-3">
    {{-- List --}}
    <h2 class="mt-4">لیست {{ $pageName }}</h2>

    {{-- Button --}}
    <button type="button" id="create_record"
        class="btn btn-primary btn-sm">+ افزودن {{ $buttonValue }}</button>

    <hr>
    
    {{-- Responsive Table --}}
    <div class="table-responsive">
        {{ $table ?? null }}
    </div>
</div>


