@extends('worker.layout')

@section('title', 'Dashboard')
@section('page-title', 'Tableau de bord')

@section('content')
<div class="content-section">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h2>Bonjour, {{ Auth::user()->name }} ! 👋</h2>
            <p>Bienvenue dans votre espace de travail {{ $userEspace->nom }}</p>
        </div>
        <div class="welcome-time">
            <span id="current-time"></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3 id="stat-total" data-target="{{ $stats['total'] }}">0</h3>
                <p>Total des tâches</p>
                <div class="stat-trend">
                    <i class="fas fa-info-circle"></i>
                    <span>Toutes vos tâches</span>
                </div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <h3 id="stat-active" data-target="{{ $stats['active'] }}">0</h3>
                <p>Tâches actives</p>
                <div class="stat-trend positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>En cours</span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total'] > 0 ? ($stats['active'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3 id="stat-pending" data-target="{{ $stats['pending'] }}">0</h3>
                <p>En attente</p>
                <div class="stat-trend neutral">
                    <i class="fas fa-minus"></i>
                    <span>À commencer</span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3 id="stat-paused" data-target="{{ $stats['paused'] }}">0</h3>
                <p>En pause</p>
                <div class="stat-trend neutral">
                    <i class="fas fa-pause"></i>
                    <span>En pause</span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total'] > 0 ? ($stats['paused'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3 id="stat-completed" data-target="{{ $stats['completed'] }}">0</h3>
                <p>Terminées</p>
                <div class="stat-trend positive">
                    <i class="fas fa-check"></i>
                    <span>Accomplies</span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="overall-progress-card">
        <div class="progress-header">
            <h3>Progression Globale</h3>
            <span id="overall-percentage">{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%</span>
        </div>
        <div class="progress-container">
            <div class="progress-track">
                <div class="progress-fill-main" id="overall-progress" style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0 }}%"></div>
            </div>
        </div>
        <div class="progress-labels">
            <span>{{ $stats['completed'] }} terminée(s)</span>
            <span>{{ $stats['total'] }} au total</span>
        </div>
    </div>

    <!-- Space Information -->
    <div class="info-section">
        <div class="section-header">
            <h3><i class="fas fa-building"></i> Informations de l'espace</h3>
        </div>
        <div class="info-card">
            <div class="info-content">
                <h4>{{ $userEspace->nom }}</h4>
                <p class="info-description">
                    {{ $userEspace->description ?? 'Aucune description disponible' }}
                </p>
                <div class="info-meta">
                    <span class="status-badge {{ $userEspace->is_active ? 'active' : 'inactive' }}">
                        {{ $userEspace->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="tasks-section">
        <div class="section-header">
            <h3><i class="fas fa-list-check"></i> Mes tâches assignées</h3>
            <div class="section-actions">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        Toutes ({{ $stats['total'] }})
                    </button>
                    <button class="filter-tab" data-filter="active">
                        Actives ({{ $stats['active'] }})
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        En attente ({{ $stats['pending'] }})
                    </button>
                    <button class="filter-tab" data-filter="paused">
                        En pause ({{ $stats['paused'] }})
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        Terminées ({{ $stats['completed'] }})
                    </button>
                </div>
            </div>
        </div>

        @if($attributions->count() > 0)
            <div class="tasks-grid" id="tasksContainer">
                @foreach($attributions as $attribution)
                    <div class="task-card" data-status="{{ $attribution->task_status ?? $attribution->status }}">
                        <div class="task-header">
                            <div class="task-type">
                                <span class="type-badge" style="background-color: {{ $attribution->type_color }}">
                                    {{ $attribution->type_display }}
                                </span>
                            </div>
                            <div class="task-status">
                                <span class="status-indicator {{ $attribution->status }}"></span>
                                <span class="status-text">{{ $attribution->status_display }}</span>
                            </div>
                        </div>

                        <div class="task-content">
                            <h4 class="task-title">
                                {{ $attribution->title ?? 'Tâche assignée dans ' . $attribution->espace->nom }}
                            </h4>
                            
                            @if($attribution->description)
                                <div class="task-description-container">
                                    <p class="task-description">
                                        {{ Str::limit($attribution->description, 150) }}
                                    </p>
                                    @if(strlen($attribution->description) > 150)
                                        <button class="read-more-btn" onclick="toggleDescription({{ $attribution->id }})">
                                            <i class="fas fa-chevron-down"></i> Lire plus
                                        </button>
                                        <p class="task-description-full" id="description-full-{{ $attribution->id }}" style="display: none;">
                                            {{ $attribution->description }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <div class="task-details-grid">
                                <div class="task-info-section">
                                    <h5 class="section-title">
                                        <i class="fas fa-info-circle"></i>
                                        Informations
                                    </h5>
                                    <div class="task-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-plus"></i>
                                            <div class="meta-content">
                                                <span class="meta-label">Créée le</span>
                                                <span class="meta-value">{{ $attribution->created_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>
                                        
                                        @if($attribution->start_date)
                                            <div class="meta-item">
                                                <i class="fas fa-play-circle"></i>
                                                <div class="meta-content">
                                                    <span class="meta-label">Date de début</span>
                                                    <span class="meta-value">{{ $attribution->start_date->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($attribution->end_date)
                                            <div class="meta-item">
                                                <i class="fas fa-calendar-times"></i>
                                                <div class="meta-content">
                                                    <span class="meta-label">Date limite</span>
                                                    <span class="meta-value {{ $attribution->end_date->isPast() ? 'overdue' : ($attribution->end_date->diffInDays() < 3 ? 'urgent' : '') }}">
                                                        {{ $attribution->end_date->format('d/m/Y') }}
                                                        @if($attribution->end_date->isPast())
                                                            <span class="status-indicator-mini overdue">En retard</span>
                                                        @elseif($attribution->end_date->diffInDays() < 3)
                                                            <span class="status-indicator-mini urgent">Urgent</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="meta-item">
                                            <i class="fas fa-building"></i>
                                            <div class="meta-content">
                                                <span class="meta-label">Espace</span>
                                                <span class="meta-value">{{ $attribution->espace->nom }}</span>
                                            </div>
                                        </div>

                                        @if($attribution->updated_at != $attribution->created_at)
                                            <div class="meta-item">
                                                <i class="fas fa-clock"></i>
                                                <div class="meta-content">
                                                    <span class="meta-label">Dernière modification</span>
                                                    <span class="meta-value">{{ $attribution->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($attribution->task_status !== 'completed')
                                    <div class="task-progress-section">
                                        <h5 class="section-title">
                                            <i class="fas fa-chart-line"></i>
                                            Progression
                                        </h5>
                                        <div class="task-timeline">
                                            <div class="timeline-item {{ ($attribution->task_status === 'pending' || !$attribution->task_status) ? 'active' : 'completed' }}">
                                                <div class="timeline-dot"></div>
                                                <div class="timeline-content">
                                                    <span class="timeline-label">En attente</span>
                                                    <span class="timeline-time">{{ $attribution->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                            <div class="timeline-item {{ $attribution->task_status === 'active' ? 'active' : ($attribution->task_status === 'completed' ? 'completed' : '') }}">
                                                <div class="timeline-dot"></div>
                                                <div class="timeline-content">
                                                    <span class="timeline-label">En cours</span>
                                                    <span class="timeline-time">
                                                        @if($attribution->task_status === 'active' || $attribution->task_status === 'completed')
                                                            Actuelle
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-item {{ $attribution->task_status === 'completed' ? 'completed' : '' }}">
                                                <div class="timeline-dot"></div>
                                                <div class="timeline-content">
                                                    <span class="timeline-label">Terminée</span>
                                                    <span class="timeline-time">
                                                        @if($attribution->task_status === 'completed')
                                                            {{ $attribution->updated_at->format('d/m/Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="task-actions">
                            <button class="btn btn-outline btn-sm" onclick="viewTask({{ $attribution->id }})">
                                <i class="fas fa-eye"></i>
                                Détails
                            </button>
                            
                            @if($attribution->task_status !== 'completed')
                                <div class="quick-actions">
                                    @if($attribution->task_status === 'pending' || !$attribution->task_status)
                                        <button class="btn btn-success btn-sm" 
                                                onclick="quickUpdateStatus({{ $attribution->id }}, 'active', this)" 
                                                title="Commencer la tâche">
                                            <i class="fas fa-play"></i>
                                            Commencer
                                        </button>
                                    @elseif($attribution->task_status === 'active')
                                        <button class="btn btn-warning btn-sm" 
                                                onclick="quickUpdateStatus({{ $attribution->id }}, 'pending', this)" 
                                                title="Mettre en pause">
                                            <i class="fas fa-pause"></i>
                                            Pause
                                        </button>
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="quickUpdateStatus({{ $attribution->id }}, 'completed', this)" 
                                                title="Marquer comme terminée">
                                            <i class="fas fa-check"></i>
                                            Terminer
                                        </button>
                                    @endif
                                </div>
                                
                                <div class="status-controls" style="margin-top: 0.5rem;">
                                    <select class="status-select" onchange="updateTaskStatus({{ $attribution->id }}, this.value)">
                                        <option value="pending" {{ ($attribution->task_status === 'pending' || !$attribution->task_status) ? 'selected' : '' }}>
                                            En attente
                                        </option>
                                        <option value="active" {{ $attribution->task_status === 'active' ? 'selected' : '' }}>
                                            En cours
                                        </option>
                                        <option value="completed">
                                            Terminée
                                        </option>
                                    </select>
                                </div>
                            @else
                                <span class="completed-badge">
                                    <i class="fas fa-check-circle"></i>
                                    Terminée
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3>Aucune tâche assignée</h3>
                <p>Aucune tâche ne vous a été assignée pour le moment dans cet espace.</p>
            </div>
        @endif
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal" id="taskModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Détails de la tâche</h3>
            <button class="modal-close" onclick="closeTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="taskModalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initializeDashboard();
    
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    updateTime();
    setInterval(updateTime, 1000);

    // Initialize dashboard functionality
    function initializeDashboard() {
        // Animate counters on load
        animateCounters();
        
        // Start auto-refresh
        startAutoRefresh();
        
        // Add loading indicator
        addLoadingIndicator();
    }

    // Animate counter numbers
    function animateCounters() {
        const counters = document.querySelectorAll('[data-target]');
        const duration = 1000; // 1 second
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            let current = 0;
            const increment = target / (duration / 16); // 60 FPS
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    }

    // Auto-refresh functionality
    function startAutoRefresh() {
        setInterval(refreshStats, 30000); // Refresh every 30 seconds
    }

    // Refresh statistics via API
    function refreshStats() {
        showLoadingIndicator();
        
        fetch('/worker/api/stats')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                updateStatsDisplay(data);
                hideLoadingIndicator();
            })
            .catch(error => {
                console.error('Error refreshing stats:', error);
                hideLoadingIndicator();
                showNotification('Erreur lors de la mise à jour des statistiques', 'error');
            });
    }

    // Update statistics display
    function updateStatsDisplay(data) {
        const stats = data.stats;
        
        // Update counters with animation
        updateCounterWithAnimation('stat-total', stats.total);
        updateCounterWithAnimation('stat-active', stats.active);
        updateCounterWithAnimation('stat-pending', stats.pending);
        updateCounterWithAnimation('stat-paused', stats.paused || 0);
        updateCounterWithAnimation('stat-completed', stats.completed);
        
        // Update progress bars
        updateProgressBar('stat-active', stats.active, stats.total);
        updateProgressBar('stat-pending', stats.pending, stats.total);
        updateProgressBar('stat-paused', stats.paused || 0, stats.total);
        updateProgressBar('stat-completed', stats.completed, stats.total);
        
        // Update overall progress
        const overallPercentage = data.progress;
        document.getElementById('overall-percentage').textContent = overallPercentage + '%';
        document.getElementById('overall-progress').style.width = overallPercentage + '%';
        
        // Update labels
        const labels = document.querySelector('.progress-labels');
        if (labels) {
            labels.innerHTML = `
                <span>${stats.completed} terminée(s)</span>
                <span>${stats.total} au total</span>
            `;
        }
    }

    // Update counter with smooth animation
    function updateCounterWithAnimation(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const currentValue = parseInt(element.textContent) || 0;
        if (currentValue === newValue) return;
        
        const duration = 500;
        const startTime = Date.now();
        const startValue = currentValue;
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easing = 1 - Math.pow(1 - progress, 3); // Ease out cubic
            
            const value = Math.round(startValue + (newValue - startValue) * easing);
            element.textContent = value;
            element.setAttribute('data-target', newValue);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        animate();
        
        // Add pulse effect for changes
        element.classList.add('stat-updated');
        setTimeout(() => element.classList.remove('stat-updated'), 600);
    }

    // Update progress bar
    function updateProgressBar(cardId, value, total) {
        const card = document.getElementById(cardId).closest('.stat-card');
        const progressBar = card.querySelector('.progress-fill');
        if (!progressBar) return;
        
        const percentage = total > 0 ? (value / total) * 100 : 0;
        progressBar.style.width = percentage + '%';
    }

    // Loading indicator functions
    function addLoadingIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'loading-indicator';
        indicator.className = 'loading-indicator hidden';
        indicator.innerHTML = `
            <div class="loading-spinner"></div>
            <span>Mise à jour...</span>
        `;
        document.body.appendChild(indicator);
    }

    function showLoadingIndicator() {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
        }
    }

    function hideLoadingIndicator() {
        const indicator = document.getElementById('loading-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
    }

    // Filter tabs functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const tasksContainer = document.getElementById('tasksContainer');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter tasks
            const tasks = tasksContainer.querySelectorAll('.task-card');
            tasks.forEach(task => {
                const status = task.getAttribute('data-status');
                let showTask = false;
                
                if (filter === 'all') {
                    showTask = true;
                } else if (filter === 'completed' && status === 'completed') {
                    showTask = true;
                } else if (filter === 'pending' && (status === 'pending' || !status)) {
                    showTask = true;
                } else if (status === filter) {
                    showTask = true;
                }
                
                task.style.display = showTask ? 'block' : 'none';
            });
        });
    });
});

// Quick action function for buttons
function quickUpdateStatus(taskId, newStatus, buttonElement) {
    const originalContent = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    buttonElement.disabled = true;
    
    updateTaskStatusAPI(taskId, newStatus)
        .then(() => {
            // Refresh stats instead of reloading page
            refreshStats();
            
            // Update task card visually
            updateTaskCardStatus(taskId, newStatus);
            
            showNotification('Statut mis à jour avec succès', 'success');
        })
        .catch(error => {
            console.error('Quick update error:', error);
            buttonElement.innerHTML = originalContent;
            buttonElement.disabled = false;
            
            let errorMessage = 'Erreur lors de la mise à jour';
            if (error.message.includes('403')) {
                errorMessage = 'Vous n\'avez pas l\'autorisation de modifier cette tâche';
            } else if (error.message.includes('404')) {
                errorMessage = 'Tâche non trouvée';
            } else if (error.message.includes('500')) {
                errorMessage = 'Erreur serveur, veuillez réessayer';
            }
            
            showNotification(errorMessage, 'error');
        });
}

// Function to update task status
function updateTaskStatus(taskId, newStatus) {
    // Show loading for the select element
    const selectElement = event.target;
    const originalValue = selectElement.value;
    selectElement.disabled = true;
    
    updateTaskStatusAPI(taskId, newStatus)
        .then(() => {
            refreshStats();
            updateTaskCardStatus(taskId, newStatus);
            showNotification('Statut mis à jour avec succès', 'success');
            selectElement.disabled = false;
        })
        .catch(error => {
            console.error('Status update error:', error);
            
            // Revert select to original value
            selectElement.value = originalValue;
            selectElement.disabled = false;
            
            let errorMessage = 'Erreur lors de la mise à jour';
            if (error.message.includes('403')) {
                errorMessage = 'Vous n\'avez pas l\'autorisation de modifier cette tâche';
            } else if (error.message.includes('404')) {
                errorMessage = 'Tâche non trouvée';
            } else if (error.message.includes('500')) {
                errorMessage = 'Erreur serveur, veuillez réessayer';
            }
            
            showNotification(errorMessage, 'error');
        });
}

// API call for status update
function updateTaskStatusAPI(taskId, newStatus) {
    return fetch(`/worker/tasks/${taskId}/update-status`, {
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
        if (!data.success) {
            throw new Error(data.error || data.message || 'Update failed');
        }
        return data;
    })
    .catch(error => {
        // Check if it's a network error
        if (error instanceof TypeError) {
            throw new Error('Erreur de connexion réseau');
        }
        throw error;
    });
}

// Update task card visual status without page reload
function updateTaskCardStatus(taskId, newStatus) {
    const taskCard = document.querySelector(`[onclick*="${taskId}"]`).closest('.task-card');
    if (!taskCard) return;
    
    // Update status indicator
    const statusIndicator = taskCard.querySelector('.status-indicator');
    const statusText = taskCard.querySelector('.status-text');
    
    if (statusIndicator) {
        statusIndicator.className = `status-indicator ${newStatus}`;
    }
    
    if (statusText) {
        const statusLabels = {
            'pending': 'En attente',
            'active': 'En cours', 
            'completed': 'Terminée'
        };
        statusText.textContent = statusLabels[newStatus] || newStatus;
    }
    
    // Update task card data attribute for filtering
    taskCard.setAttribute('data-status', newStatus);
    
    // Add visual feedback
    taskCard.classList.add('status-updated');
    setTimeout(() => taskCard.classList.remove('status-updated'), 1000);
}

// Function to view task details
function viewTask(taskId) {
    window.location.href = `/worker/tasks/${taskId}`;
}

// Function to close task modal
function closeTaskModal() {
    document.getElementById('taskModal').style.display = 'none';
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

// Function to toggle task description
function toggleDescription(taskId) {
    const shortDescription = document.querySelector(`[onclick="toggleDescription(${taskId})"]`).previousElementSibling;
    const fullDescription = document.getElementById(`description-full-${taskId}`);
    const button = document.querySelector(`[onclick="toggleDescription(${taskId})"]`);
    
    if (fullDescription.style.display === 'none') {
        shortDescription.style.display = 'none';
        fullDescription.style.display = 'block';
        button.innerHTML = '<i class="fas fa-chevron-up"></i> Lire moins';
    } else {
        shortDescription.style.display = 'block';
        fullDescription.style.display = 'none';
        button.innerHTML = '<i class="fas fa-chevron-down"></i> Lire plus';
    }
}
</script>

<style>
.welcome-section {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.welcome-content h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.8rem;
    font-weight: 600;
}

.welcome-content p {
    margin: 0;
    opacity: 0.9;
}

.welcome-time {
    font-size: 0.9rem;
    opacity: 0.8;
    text-align: right;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.tasks-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.filter-tab.active,
.filter-tab:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.tasks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.task-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.2s;
}

.task-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 0.5rem;
}

.status-indicator.active { background-color: #10b981; }
.status-indicator.pending { background-color: #f59e0b; }
.status-indicator.paused { background-color: #f97316; }
.status-indicator.completed { background-color: #6b7280; }
.status-indicator.expired { background-color: #6b7280; }

.task-content {
    margin-bottom: 1.5rem;
}

.task-title {
    margin: 0 0 0.75rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
}

.task-description {
    margin: 0 0 1rem 0;
    color: #6b7280;
    line-height: 1.5;
}

.task-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.meta-item i {
    width: 16px;
    text-align: center;
}

.task-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.status-select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}

.info-section {
    background: white;
    border-radius: 12px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.info-card {
    padding: 1.5rem;
}

.info-content h4 {
    margin: 0 0 0.75rem 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-color);
}

.info-description {
    margin: 0 0 1rem 0;
    color: #6b7280;
    line-height: 1.6;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
    color: #374151;
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

/* Loading Indicator */
.loading-indicator {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(59, 130, 246, 0.95);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    z-index: 1000;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.loading-indicator.hidden {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
}

.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Status update animations */
.stat-updated {
    animation: pulse-highlight 0.6s ease-out;
}

@keyframes pulse-highlight {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); background-color: #dbeafe; }
    100% { transform: scale(1); }
}

.status-updated {
    animation: card-flash 1s ease-out;
}

@keyframes card-flash {
    0% { background-color: white; }
    50% { background-color: #f0f9ff; }
    100% { background-color: white; }
}

/* Quick actions styling */
.quick-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover:not(:disabled) {
    background: #059669;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover:not(:disabled) {
    background: #d97706;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #2563eb;
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover:not(:disabled) {
    background: #f9fafb;
    color: #374151;
}

.completed-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Enhanced Task Details Styles */
.task-description-container {
    margin-bottom: 1.5rem;
}

.task-description-full {
    margin: 0.75rem 0 0 0;
    color: #6b7280;
    line-height: 1.6;
}

.read-more-btn {
    background: none;
    border: none;
    color: #3b82f6;
    cursor: pointer;
    font-size: 0.875rem;
    padding: 0.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    transition: color 0.2s ease;
}

.read-more-btn:hover {
    color: #2563eb;
}

.task-details-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-top: 1rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f3f4f6;
}

.task-info-section .task-meta {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.meta-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.meta-item i {
    width: 16px;
    text-align: center;
    color: #6b7280;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.meta-content {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    flex: 1;
}

.meta-label {
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-weight: 500;
}

.meta-value {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.meta-value.overdue {
    color: #dc2626;
}

.meta-value.urgent {
    color: #f59e0b;
}

.status-indicator-mini {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-left: 0.5rem;
}

.status-indicator-mini.overdue {
    background: #fee2e2;
    color: #991b1b;
}

.status-indicator-mini.urgent {
    background: #fef3c7;
    color: #92400e;
}

/* Task Timeline Styles */
.task-progress-section {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
}

.task-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
}

.task-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 16px;
    bottom: 16px;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    z-index: 1;
}

.timeline-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #e5e7eb;
    background: white;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.timeline-item.active .timeline-dot {
    border-color: #3b82f6;
    background: #3b82f6;
}

.timeline-item.completed .timeline-dot {
    border-color: #10b981;
    background: #10b981;
}

.timeline-content {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.timeline-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.timeline-item.active .timeline-label {
    color: #3b82f6;
    font-weight: 600;
}

.timeline-item.completed .timeline-label {
    color: #10b981;
    font-weight: 600;
}

.timeline-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* Enhanced Task Card Layout */
.task-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.task-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #e5e7eb, #f3f4f6);
    transition: all 0.3s ease;
}

.task-card[data-status="active"]::before {
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
}

.task-card[data-status="pending"]::before {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.task-card[data-status="completed"]::before {
    background: linear-gradient(90deg, #10b981, #059669);
}

.task-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transform: translateY(-4px);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .task-details-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .task-timeline {
        gap: 0.75rem;
    }
    
    .meta-item {
        gap: 0.5rem;
    }
    
    .timeline-item {
        gap: 0.75rem;
    }
    
    .task-progress-section {
        padding: 0.75rem;
    }
    
    .section-title {
        font-size: 0.8rem;
    }
}

@media (max-width: 768px) {
    .welcome-section {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .filter-tabs {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .filter-tab {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .tasks-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
    }
    
    .task-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .quick-actions {
        justify-content: center;
    }
    
    .loading-indicator {
        left: 20px;
        right: 20px;
        transform: none;
    }
    
    .loading-indicator.hidden {
        transform: translateY(-20px);
    }
}
</style>
@endpush
@endsection