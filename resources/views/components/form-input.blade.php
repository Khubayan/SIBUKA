@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
           class="form-control @error($name) is-invalid @enderror"
           value="{{ old($name, $value) }}"
           placeholder="{{ $placeholder }}"
           @if($required) required @endif
           {{ $attributes }}>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
