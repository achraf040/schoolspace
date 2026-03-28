@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'value' => null
])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span style="color: #ef4444;">*</span>
        @endif
    </label>
    
    <input type="{{ $type }}" 
           id="{{ $name }}" 
           name="{{ $name }}" 
           class="form-input @error($name) error @enderror" 
           value="{{ old($name, $value) }}" 
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes }}>
    
    @error($name)
        <div class="error-message">{{ $message }}</div>
    @enderror
    
    @if($help)
        <div class="form-help">{{ $help }}</div>
    @endif
</div>