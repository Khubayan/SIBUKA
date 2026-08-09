@props(['name', 'label', 'options' => [], 'selected' => '', 'required' => false, 'placeholder' => 'Pilih...'])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    <select name="{{ $name }}" id="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            @if($required) required @endif
            {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
