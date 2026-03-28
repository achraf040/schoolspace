@props([
    'title',
    'backRoute' => null,
    'backText' => 'Retour à la liste'
])

<div class="section-header">
    <h2 class="section-title">{{ $title }}</h2>
    @if($backRoute)
        <a href="{{ $backRoute }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            {{ $backText }}
        </a>
    @endif
    {{ $slot }}
</div>