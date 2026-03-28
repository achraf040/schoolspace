@props([
    'title',
    'value',
    'icon',
    'type' => 'primary',
    'trend' => null,
    'breakdown' => null
])

@php
$typeClasses = [
    'primary' => 'users',
    'success' => 'espaces', 
    'warning' => 'attributions',
    'info' => 'performance'
];
$cardClass = $typeClasses[$type] ?? 'users';
@endphp

<div class="stat-card {{ $cardClass }}">
    <div class="stat-content">
        <div class="stat-icon">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
        <div class="stat-details">
            <div class="stat-number">{{ $value }}</div>
            <div class="stat-label">{{ $title }}</div>
            @if($breakdown)
                <div class="stat-breakdown">
                    {{ $breakdown }}
                </div>
            @endif
        </div>
    </div>
    
    @if($trend)
        <div class="stat-trend {{ $trend['type'] ?? 'neutral' }}">
            <i class="fas fa-{{ $trend['icon'] ?? 'minus' }}"></i>
            <span>{{ $trend['text'] }}</span>
        </div>
    @endif
    
    {{ $slot }}
</div>