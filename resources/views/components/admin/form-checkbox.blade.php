@props([
    'name',
    'label',
    'help' => null,
    'checked' => false
])

<div class="form-group">
    <!-- Hidden input to ensure checkbox value is always sent -->
    <input type="hidden" name="{{ $name }}" value="0">
    <div class="checkbox-item">
        <input type="checkbox" 
               id="{{ $name }}" 
               name="{{ $name }}" 
               value="1"
               {{ $checked ? 'checked' : '' }}
               {{ $attributes }}>
        <label for="{{ $name }}">{{ $label }}</label>
    </div>
    
    @if($help)
        <div class="form-help">{{ $help }}</div>
    @endif
</div>