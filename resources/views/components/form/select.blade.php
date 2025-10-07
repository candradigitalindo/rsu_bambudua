{{-- Standard Select Component --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'help' => null,
    'errors' => null,
    'class' => '',
    'labelClass' => '',
    'selectClass' => '',
    'containerClass' => 'mb-3',
    'optionValue' => null,
    'optionLabel' => null
])

@php
$hasError = $errors && $errors->has($name);
$inputId = $attributes['id'] ?? $name . '_' . uniqid();
$selectedValue = old($name, $selected);
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

    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $inputId }}"
        class="form-select {{ $selectClass }} {{ $hasError ? 'is-invalid' : '' }} {{ $class }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        {{ $attributes->except(['class', 'id']) }}
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if(is_array($options) || $options instanceof \Illuminate\Support\Collection)
            @foreach($options as $option)
                @if(is_object($option))
                    @php
                        $value = $optionValue ? $option->{$optionValue} : $option->id;
                        $text = $optionLabel ? $option->{$optionLabel} : $option->name;
                        $isSelected = $multiple 
                            ? (is_array($selectedValue) && in_array($value, $selectedValue))
                            : ($selectedValue == $value);
                    @endphp
                    <option value="{{ $value }}" @if($isSelected) selected @endif>
                        {{ $text }}
                    </option>
                @elseif(is_array($option))
                    <option value="{{ $option['value'] ?? $option[0] }}" 
                            @if(($multiple && is_array($selectedValue) && in_array($option['value'] ?? $option[0], $selectedValue)) || 
                                (!$multiple && $selectedValue == ($option['value'] ?? $option[0]))) selected @endif>
                        {{ $option['label'] ?? $option['text'] ?? $option[1] ?? $option[0] }}
                    </option>
                @else
                    <option value="{{ is_numeric($loop->index) ? $loop->index : $option }}" 
                            @if(($multiple && is_array($selectedValue) && in_array($option, $selectedValue)) || 
                                (!$multiple && $selectedValue == $option)) selected @endif>
                        {{ $option }}
                    </option>
                @endif
            @endforeach
        @endif
    </select>

    @if($hasError)
        <div class="invalid-feedback d-block">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if($help && !$hasError)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>