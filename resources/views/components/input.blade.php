<div class="{{ $class ?? '' }}">
    {{-- Label --}}
    <label for="{{ $key }}">
        {{ $placeholder }}
        @if ($required)  
            <span class="input-required">*</span>
        @endif
    </label>
    {{-- Input --}}
    <input type="{{ $type ?? 'text' }}" name="{{ $key }}" id="{{ $key }}"
           value="{{ $value ?? '' }}" class="form-control" placeholder="{{ $placeholder }}">
</div>