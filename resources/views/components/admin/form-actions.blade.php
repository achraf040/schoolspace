@props([
    'cancelRoute',
    'submitText' => 'Enregistrer',
    'cancelText' => 'Annuler'
])

<div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #e5e7eb;">
    <a href="{{ $cancelRoute }}" class="btn btn-secondary">
        <i class="fas fa-times"></i>
        {{ $cancelText }}
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i>
        {{ $submitText }}
    </button>
</div>