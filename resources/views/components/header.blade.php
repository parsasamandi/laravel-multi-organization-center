<div class="container-fluid mt-3">
    {{-- List --}}
    <h2 class="mt-4">{{ $pageName }}</h2>

    {{-- Button (Type 0 = Center access | Type 1 = Golestan team)--}}
    @if(Auth::user()->type == $type)
        <button type="button" id="create_record"
            class="btn btn-primary btn-sm">+ افزودن {{ $buttonValue }}</button>

        <hr>
    @endif
    
    {{-- Responsive Table --}}
    <div class="table-responsive">
        {{ $table ?? null }}
    </div>
</div>