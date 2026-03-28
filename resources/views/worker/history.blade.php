@extends('worker.layout')

@section('title', 'Historique')
@section('page-title', 'Historique des Tâches')

@section('content')
<div class="content-section">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h2 class="section-title">
                <i class="fas fa-history"></i>
                Historique des Tâches
            </h2>
            <p class="section-subtitle">Espace : {{ $userEspace->nom }}</p>
        </div>
        <div class="header-stats">
            <span class="stat-badge completed">{{ $completedTasks->total() }} terminée(s)</span>
        </div>
    </div>

    <!-- History List -->
    <div class="history-container">
        @if($completedTasks->count() > 0)
            <div class="history-list">
                @foreach($completedTasks as $task)
                <div class="history-item">
                    <div class="history-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <div class="history-content">
                        <div class="history-header">
                            <h3 class="history-title">{{ $task->title ?? 'Tâche #' . $task->id }}</h3>
                            <span class="completion-date">
                                <i class="fas fa-clock"></i>
                                Terminée {{ $task->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        @if($task->description)
                            <p class="history-description">{{ $task->description }}</p>
                        @endif
                        
                        <div class="history-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Créée le {{ $task->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Terminée le {{ $task->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-building"></i>
                                <span>{{ $task->espace->nom }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="history-actions">
                        <a href="{{ route('worker.tasks.show', $task) }}" class="btn btn-sm btn-outline">
                            <i class="fas fa-eye"></i> Voir détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                {{ $completedTasks->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-history empty-icon"></i>
                <h3>Aucune tâche terminée</h3>
                <p>Votre historique des tâches terminées apparaîtra ici.</p>
                <a href="{{ route('worker.tasks') }}" class="btn btn-primary">
                    <i class="fas fa-tasks"></i>
                    Voir mes tâches actives
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .section-subtitle {
        color: #6b7280;
        margin: 0;
        font-size: 0.875rem;
    }

    .header-stats {
        display: flex;
        gap: 1rem;
    }

    .stat-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stat-badge.completed {
        background: #dcfce7;
        color: #166534;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .history-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .history-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .history-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #166534;
        font-size: 1.125rem;
    }

    .history-content {
        flex: 1;
        min-width: 0;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        gap: 1rem;
    }

    .history-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        flex: 1;
    }

    .completion-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #10b981;
        font-size: 0.875rem;
        font-weight: 500;
        flex-shrink: 0;
    }

    .history-description {
        color: #6b7280;
        line-height: 1.6;
        margin: 0 0 1rem 0;
    }

    .history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .meta-item i {
        font-size: 0.75rem;
        width: 12px;
    }

    .history-actions {
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-outline {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
        color: #374151;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #374151;
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        margin: 0 0 1.5rem 0;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            gap: 1rem;
        }

        .history-item {
            flex-direction: column;
            gap: 1rem;
        }

        .history-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .completion-date {
            align-self: flex-start;
        }

        .history-meta {
            flex-direction: column;
            gap: 0.5rem;
        }

        .history-actions {
            align-self: stretch;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
@endsection