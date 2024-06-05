<div class="{{ $class ?? null }}">
    {{-- Label--}}
    <label for="{{ $key }}">{{ $placeholder }}</label>
    {{-- Input --}}
    <input type="{{ $type ?? 'text' }}" name="{{ $key }}" id="{{ $key }}" 
            value="{{ $value ?? null }}" class="form-control" placeholder="{{ $placeholder }}">
</div>