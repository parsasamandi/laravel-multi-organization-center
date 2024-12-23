@php
  $key = $key ?? false;
@endphp

<div class="{{ $class ?? '' }}">
    {{-- Label --}}
    <label for="{{ $key }}">
        {{ $placeholder }}:
        @if ($required ?? null)
            <span class="input-required">*</span>
        @endif
    </label>
    {{-- Input --}}
    <input type="{{ $type ?? 'text' }}" name="{{ $key }}" id="{{ $key }}"
           value="{{ $value ?? '' }}" class="form-control" placeholder="{{ $placeholder }}" @if(isset($readonly) && $readonly) readonly @endif>
</div>