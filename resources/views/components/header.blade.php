<div class="container-fluid mt-3">
    {{-- عنوان صفحه --}}
    <h2 class="mt-3">{{ $pageName }}</h2>

    @if(Auth::user()->type == $type)
        @if($buttonValue != "null")
            <button type="button" id="create_record"
                class="btn btn-primary btn-sm">+ افزودن {{ $buttonValue }}</button>
        @endif
        <hr>
    @endif
    
    {{-- جدول ریسپانسیو --}}
    <div>
        {{ $table ?? null }}
    </div>
</div>
