@props([
    'name',
    'label',
    'required' => false,
    'placeholder' => '',
    'help' => null
])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required)
            <span style="color: #ef4444;">*</span>
        @endif
    </label>
    
    <div class="password-group">
        <input type="password" 
               id="{{ $name }}" 
               name="{{ $name }}" 
               class="form-input @error($name) error @enderror" 
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               {{ $attributes }}>
        <button type="button" class="password-toggle" onclick="togglePasswordField('{{ $name }}')">
            <i id="{{ $name }}-icon" class="fas fa-eye"></i>
        </button>
    </div>
    
    @error($name)
        <div class="error-message">{{ $message }}</div>
    @enderror
    
    @if($help)
        <div class="form-help">{{ $help }}</div>
    @endif
</div>