{{-- Standard Input Component --}}
@props([
    'name',
    'type' => 'text',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'help' => null,
    'prepend' => null,
    'append' => null,
    'errors' => null,
    'class' => '',
    'labelClass' => '',
    'inputClass' => '',
    'containerClass' => 'mb-3'
])

@php
$hasError = $errors && $errors->has($name);
$inputId = $attributes['id'] ?? $name . '_' . uniqid();
@endphp

<div class="{{ $containerClass }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($prepend || $append)
        <div class="input-group {{ $hasError ? 'has-validation' : '' }}">
    @endif

    @if($prepend)
        <span class="input-group-text">
            {!! $prepend !!}
        </span>
    @endif

    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-control {{ $inputClass }} {{ $hasError ? 'is-invalid' : '' }} {{ $class }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        {{ $attributes->except(['class', 'id']) }}
    >

    @if($append)
        <span class="input-group-text">
            {!! $append !!}
        </span>
    @endif

    @if($prepend || $append)
        </div>
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if($help && !$hasError)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>