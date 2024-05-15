<div class="{{ $class ?? null }}">
    {{-- Label --}}
    <label for="{{ $key }}">{{ $placeholder ? (string) $placeholder : '' }}:</label>
    {{-- Textarea --}}
    <textarea name="{{ $key }}" id="{{ $key }}" rows="{{ $rows ?? 2 }}" class="form-control"
        placeholder="{{ $placeholder ? (string) $placeholder : '' }}"
        @if(isset($readonly) && $readonly) readonly @endif>{{ isset($value) && $value instanceof \Illuminate\Support\Optional ? $value->value() : $value ?? null }}</textarea>
</div>
