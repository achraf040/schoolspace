@extends('worker.layout')

@section('title', 'Mes Tâches')
@section('page-title', 'Mes Tâches')

@section('content')
<div class="content-section">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h2 class="section-title">
                <i class="fas fa-tasks"></i>
                Mes Tâches Actives
            </h2>
            <p class="section-subtitle">Espace : {{ $userEspace->nom }}</p>
        </div>
        <div class="header-stats">
            <span class="stat-badge">{{ $activeTasks->total() }} tâche(s)</span>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="tasks-container">
        @if($activeTasks->count() > 0)
            <div class="tasks-grid">
                @foreach($activeTasks as $task)
                <div class="task-card" data-status="{{ $task->task_status ?? 'pending' }}">
                    <div class="task-header">
                        <h3 class="task-title">{{ $task->title ?? 'Tâche #' . $task->id }}</h3>
                        <span class="status-badge {{ $task->task_status ?? 'pending' }}">
                            @if($task->task_status === 'active')
                                <i class="fas fa-play"></i> Active
                            @elseif($task->task_status === 'pending' || !$task->task_status)
                                <i class="fas fa-clock"></i> En attente
                            @else
                                <i class="fas fa-question"></i> {{ ucfirst($task->task_status) }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="task-content">
                        @if($task->description)
                            <p class="task-description">{{ Str::limit($task->description, 150) }}</p>
                        @endif
                        
                        <div class="task-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $task->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-building"></i>
                                <span>{{ $task->espace->nom }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="task-actions">
                        @if(!$task->task_status || $task->task_status === 'pending')
                            <button class="btn btn-sm btn-success" onclick="updateTaskStatus({{ $task->id }}, 'active')">
                                <i class="fas fa-play"></i> Commencer
                            </button>
                        @elseif($task->task_status === 'active')
                            <button class="btn btn-sm btn-warning" onclick="updateTaskStatus({{ $task->id }}, 'paused')" title="Mettre en pause">
                                <i class="fas fa-pause"></i> Pause
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                <i class="fas fa-check"></i> Terminer
                            </button>
                        @elseif($task->task_status === 'paused')
                            <button class="btn btn-sm btn-success" onclick="updateTaskStatus({{ $task->id }}, 'active')" title="Reprendre la tâche">
                                <i class="fas fa-play"></i> Reprendre
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                <i class="fas fa-check"></i> Terminer
                            </button>
                        @elseif($task->task_status === 'completed')
                            <button class="btn btn-sm btn-secondary" onclick="updateTaskStatus({{ $task->id }}, 'pending')" title="Réactiver la tâche">
                                <i class="fas fa-redo"></i> Réactiver
                            </button>
                        @endif
                        
                        <a href="{{ route('worker.tasks.show', $task) }}" class="btn btn-sm btn-outline">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container">
                {{ $activeTasks->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-tasks empty-icon"></i>
                <h3>Aucune tâche active</h3>
                <p>Vous n'avez actuellement aucune tâche active ou en attente.</p>
                <a href="{{ route('worker.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Retour au dashboard
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
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .tasks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .task-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .task-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        flex: 1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .task-description {
        color: #6b7280;
        line-height: 1.6;
        margin: 0 0 1rem 0;
    }

    .task-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
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

    .task-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
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

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
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
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    .btn-warning:hover {
        background: #d97706;
    }
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    .btn-secondary:hover {
        background: #4b5563;
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

        .tasks-grid {
            grid-template-columns: 1fr;
        }

        .task-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .task-actions {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function updateTaskStatus(taskId, newStatus) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
    button.disabled = true;
    
    fetch(`/worker/tasks/${taskId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Statut mis à jour avec succès', 'success');
            
            // Reload page after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            button.innerHTML = originalContent;
            button.disabled = false;
            showNotification(data.error || data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalContent;
        button.disabled = false;
        
        let errorMessage = 'Erreur lors de la mise à jour';
        if (error.message.includes('403')) {
            errorMessage = 'Accès non autorisé';
        } else if (error.message.includes('422')) {
            errorMessage = 'Données invalides';
        } else if (error.message.includes('500')) {
            errorMessage = 'Erreur serveur, veuillez réessayer';
        }
        
        showNotification(errorMessage, 'error');
    });
}

function showNotification(message, type = 'info') {
    // Simple notification system
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        font-weight: 500;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection