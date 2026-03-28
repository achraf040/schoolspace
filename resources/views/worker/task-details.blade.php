@extends('worker.layout')

@section('title', 'Détails de la tâche')
@section('page-title', 'Détails de la tâche')

@section('content')
<div class="content-section">
    <!-- Back Button -->
    <div class="back-section">
        <a href="{{ route('worker.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour au tableau de bord
        </a>
    </div>

    <!-- Task Details Card -->
    <div class="task-details-card">
        <div class="task-header">
            <div class="task-title-section">
                <h2>{{ $attribution->title ?? 'Tâche assignée dans ' . $attribution->espace->nom }}</h2>
                <div class="task-badges">
                    <span class="type-badge" style="background-color: {{ $attribution->type_color }}">
                        {{ $attribution->type_display }}
                    </span>
                    <span class="status-badge {{ $attribution->status }}">
                        <i class="status-indicator"></i>
                        {{ $attribution->status_display }}
                    </span>
                </div>
            </div>
        </div>

        <div class="task-content">
            <!-- Description -->
            @if($attribution->description)
                <div class="content-section">
                    <h3><i class="fas fa-file-text"></i> Description</h3>
                    <div class="description-content">
                        {{ $attribution->description }}
                    </div>
                </div>
            @endif

            <!-- Task Information -->
            <div class="content-section">
                <h3><i class="fas fa-info-circle"></i> Informations de la tâche</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Espace assigné</label>
                        <div class="info-value">
                            <i class="fas fa-building"></i>
                            {{ $attribution->espace->nom }}
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label>Type d'attribution</label>
                        <div class="info-value">
                            <span class="type-badge-sm" style="background-color: {{ $attribution->type_color }}">
                                {{ $attribution->type_display }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label>Date de création</label>
                        <div class="info-value">
                            <i class="fas fa-calendar-plus"></i>
                            {{ $attribution->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    
                    @if($attribution->start_date)
                        <div class="info-item">
                            <label>Date de début</label>
                            <div class="info-value">
                                <i class="fas fa-calendar-day"></i>
                                {{ $attribution->start_date->format('d/m/Y') }}
                            </div>
                        </div>
                    @endif
                    
                    @if($attribution->end_date)
                        <div class="info-item">
                            <label>Date de fin</label>
                            <div class="info-value">
                                <i class="fas fa-calendar-times"></i>
                                {{ $attribution->end_date->format('d/m/Y') }}
                            </div>
                        </div>
                    @endif
                    
                    @if($attribution->access_hours)
                        <div class="info-item full-width">
                            <label>Heures d'accès</label>
                            <div class="info-value">
                                <i class="fas fa-clock"></i>
                                {{ is_array($attribution->access_hours) ? implode(', ', $attribution->access_hours) : $attribution->access_hours }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Management -->
            @if($attribution->task_status !== 'completed')
                <div class="content-section">
                    <h3><i class="fas fa-tasks"></i> Gestion du statut</h3>
                    <div class="status-management">
                        <div class="current-status">
                            <label>Statut actuel:</label>
                            <span class="status-badge {{ $attribution->status }}">
                                <i class="status-indicator"></i>
                                {{ $attribution->status_display }}
                            </span>
                        </div>
                        
                        <div class="status-update">
                            <label for="statusSelect">Changer le statut:</label>
                            <div class="status-controls">
                                <select id="statusSelect" class="status-select">
                                    <option value="pending" {{ $attribution->task_status === 'pending' || !$attribution->task_status ? 'selected' : '' }}>
                                        En attente
                                    </option>
                                    <option value="active" {{ $attribution->task_status === 'active' ? 'selected' : '' }}>
                                        En cours
                                    </option>
                                    <option value="paused" {{ $attribution->task_status === 'paused' ? 'selected' : '' }}>
                                        En pause
                                    </option>
                                    <option value="completed" {{ $attribution->task_status === 'completed' ? 'selected' : '' }}>
                                        Terminée
                                    </option>
                                </select>
                                <button class="btn btn-primary" onclick="updateTaskStatus()">
                                    <i class="fas fa-save"></i>
                                    Mettre à jour
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Timeline -->
            <div class="content-section">
                <h3><i class="fas fa-history"></i> Historique</h3>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker created"></div>
                        <div class="timeline-content">
                            <h4>Tâche créée</h4>
                            <p>La tâche a été créée et assignée à votre espace</p>
                            <span class="timeline-date">{{ $attribution->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>
                    
                    @if($attribution->updated_at->ne($attribution->created_at))
                        <div class="timeline-item">
                            <div class="timeline-marker updated"></div>
                            <div class="timeline-content">
                                <h4>Dernière mise à jour</h4>
                                <p>Statut ou informations modifiés</p>
                                <span class="timeline-date">{{ $attribution->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateTaskStatus() {
    const statusSelect = document.getElementById('statusSelect');
    const newStatus = statusSelect.value;
    
    if (!newStatus) {
        showNotification('Veuillez sélectionner un statut', 'error');
        return;
    }
    
    // Confirmation for completed status
    if (newStatus === 'completed') {
        if (!confirm('Êtes-vous sûr de vouloir marquer cette tâche comme terminée ? Cette action ne peut pas être annulée.')) {
            return;
        }
    }
    
    fetch(`/worker/tasks/{{ $attribution->id }}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Statut mis à jour avec succès', 'success');
            
            // Redirect to dashboard after success
            setTimeout(() => {
                window.location.href = '{{ route("worker.dashboard") }}';
            }, 1500);
        } else {
            showNotification('Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

// Function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Hide and remove notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>

<style>
.back-section {
    margin-bottom: 1.5rem;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.back-btn:hover {
    background: var(--primary-color);
    color: white;
}

.task-details-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.task-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 2rem;
}

.task-title-section h2 {
    margin: 0 0 1rem 0;
    font-size: 1.8rem;
    font-weight: 600;
}

.task-badges {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.type-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.task-content {
    padding: 2rem;
}

.content-section {
    margin-bottom: 2rem;
}

.content-section:last-child {
    margin-bottom: 0;
}

.content-section h3 {
    margin: 0 0 1rem 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.description-content {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
    line-height: 1.6;
    color: #374151;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    display: block;
    margin-bottom: 0.5rem;
}

.info-value {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.type-badge-sm {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-management {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.current-status {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.current-status label {
    font-weight: 500;
    color: #374151;
}

.current-status .status-badge {
    background: var(--primary-color);
}

.status-update label {
    font-weight: 500;
    color: #374151;
    display: block;
    margin-bottom: 0.5rem;
}

.status-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.status-select {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    font-size: 0.875rem;
    min-width: 150px;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -1.75rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e5e7eb;
}

.timeline-marker.created {
    background: var(--primary-color);
    box-shadow: 0 0 0 2px var(--primary-color);
}

.timeline-marker.updated {
    background: #f59e0b;
    box-shadow: 0 0 0 2px #f59e0b;
}

.timeline-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.timeline-content p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    line-height: 1.5;
}

.timeline-date {
    font-size: 0.875rem;
    color: #9ca3af;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 6px;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 1000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification.success {
    background: #10b981;
}

.notification.error {
    background: #ef4444;
}

.notification.info {
    background: #3b82f6;
}

@media (max-width: 768px) {
    .task-header {
        padding: 1.5rem;
    }
    
    .task-title-section h2 {
        font-size: 1.5rem;
    }
    
    .task-badges {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .task-content {
        padding: 1.5rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .status-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
    }
}
</style>
@endpush
@endsection