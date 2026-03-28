@props([
    'route',
    'title',
    'description',
    'icon',
    'type' => 'primary'
])

<a href="{{ $route }}" class="quick-action-card {{ $type }}">
    <div class="action-icon">
        <i class="fas fa-{{ $icon }}"></i>
    </div>
    <div class="action-content">
        <h3>{{ $title }}</h3>
        <p>{{ $description }}</p>
    </div>
    <div class="action-arrow">
        <i class="fas fa-arrow-right"></i>
    </div>
</a>