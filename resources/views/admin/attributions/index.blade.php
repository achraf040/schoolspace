@extends('admin.layout')

@section('title', 'Gestion des Tâches')
@section('page-title', 'Gestion des Tâches')

@section('content')
<div class="content-section">
    <div class="section-header">
        <div class="header-top">
            <h2 class="section-title">
                <i class="fas fa-tasks"></i>
                Gestion des Tâches
            </h2>
        </div>
        
        <div class="filters-container">
            <div class="filters-row">
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchAttributions" placeholder="Rechercher une tâche...">
                    </div>
                </div>
                
                <div class="filter-group">
                    <div class="filter-item">
                        <label class="filter-label">Espace</label>
                        <div class="space-filter-container">
                            <button class="space-filter-toggle" id="spaceFilterToggle">
                                <i class="fas fa-building"></i>
                                <span id="spaceFilterText">Tous les espaces</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="space-filter-dropdown" id="spaceFilterDropdown">
                                <div class="space-filter-header">
                                    <input type="text" id="spaceSearch" placeholder="Rechercher un espace..." class="space-search-input">
                                </div>
                                <div class="space-filter-list">
                                    <div class="space-filter-item active" data-space-id="" onclick="selectSpace('', 'Tous les espaces')">
                                        <div class="space-item-icon all-spaces">
                                            <i class="fas fa-th-large"></i>
                                        </div>
                                        <div class="space-item-info">
                                            <div class="space-item-name">Tous les espaces</div>
                                            <div class="space-item-count">{{ $attributions->count() }} tâches</div>
                                        </div>
                                    </div>
                                    @forelse($espaces as $espace)
                                        <div class="space-filter-item" data-space-id="{{ $espace->id }}" data-space-name="{{ strtolower($espace->nom) }}" onclick="selectSpace('{{ $espace->id }}', '{{ $espace->nom }}')">
                                            <div class="space-item-icon">
                                                <i class="fas fa-building"></i>
                                                <div class="space-status-dot {{ $espace->is_active ? 'active' : 'inactive' }}"></div>
                                            </div>
                                            <div class="space-item-info">
                                                <div class="space-item-name">{{ $espace->nom }}</div>
                                                <div class="space-item-type">{{ $espace->space_type }}</div>
                                                <div class="space-item-count">{{ $espace->attributions()->count() }} tâches</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="space-filter-empty">Aucun espace disponible</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="filter-item">
                        <label class="filter-label">Type</label>
                        <select id="typeFilter" class="filter-select">
                            <option value="">Tous les types</option>
                            <option value="permanente">🏠 Permanente</option>
                            <option value="ponctuelle">📅 Ponctuelle</option>
                            <option value="temporaire">⏰ Temporaire</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="view-controls">
                <button class="btn btn-outline btn-clear-filters" id="clearFiltersBtn" onclick="clearAllFilters()" title="Effacer tous les filtres">
                    <i class="fas fa-times"></i>
                    <span class="hide-mobile">Effacer</span>
                </button>
                <div class="view-toggle">
                    <button class="view-btn active" id="listView" onclick="toggleView('list')">
                        <i class="fas fa-list"></i>
                        <span>Liste</span>
                    </button>
                    <button class="view-btn" id="cardView" onclick="toggleView('card')">
                        <i class="fas fa-th-large"></i>
                        <span>Cartes</span>
                    </button>
                </div>
                <button class="btn btn-primary btn-new-task" onclick="showNewAttribution()">
                    <i class="fas fa-plus"></i>
                    Nouvelle Tâche
                </button>
            </div>
        </div>
    </div>

    @if($attributions->count() > 0)
        <!-- Statistiques simples -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Total:</span>
                <span class="stat-value">{{ $attributions->total() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Utilisateurs:</span>
                <span class="stat-value text-success">{{ \App\Models\User::where('role', 'user')->where('is_active', true)->count() }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Espaces:</span>
                <span class="stat-value text-warning">{{ $espaces->count() }}</span>
            </div>
        </div>
    @endif

    <div class="form-container">
        @if($attributions->count() > 0)
            <!-- Vue en liste compacte (par défaut) -->
            <div class="tasks-list-view" id="tasksListView">
                <div class="tasks-table">
                    <div class="table-header">
                        <div class="th-type">Type</div>
                        <div class="th-user">Utilisateur</div>
                        <div class="th-space">Espace</div>
                        <div class="th-period">Période</div>
                        <div class="th-status">Statut</div>
                        <div class="th-actions">Actions</div>
                    </div>
                    @foreach($attributions as $attribution)
                    <div class="task-row" 
                         data-user-id="{{ $attribution->user_id }}" 
                         data-espace-id="{{ $attribution->espace_id }}"
                         data-user-name="{{ strtolower($attribution->user->name ?? '') }}"
                         data-espace-name="{{ strtolower($attribution->espace->nom ?? '') }}"
                         data-type="{{ $attribution->type }}">
                        
                        <div class="td-type">
                            <span class="type-badge-small {{ $attribution->type }}">
                                @if($attribution->type === 'permanente')
                                    <i class="fas fa-infinity"></i>
                                @elseif($attribution->type === 'ponctuelle')
                                    <i class="fas fa-calendar-day"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                                {{ ucfirst($attribution->type) }}
                            </span>
                        </div>
                        
                        <div class="td-user">
                            <div class="user-compact">
                                <div class="user-avatar-small">
                                    @if($attribution->user && $attribution->user->hasProfilePhoto())
                                        <img src="{{ $attribution->user->profile_photo_url }}" alt="{{ $attribution->user->name }}">
                                    @elseif($attribution->user)
                                        <div class="avatar-initials-small">{{ strtoupper(substr($attribution->user->name, 0, 1)) }}</div>
                                    @else
                                        <div class="avatar-initials-small">?</div>
                                    @endif
                                    <div class="status-dot {{ $attribution->user && $attribution->user->is_active ? 'active' : 'inactive' }}"></div>
                                </div>
                                <div class="user-info-compact">
                                    <div class="user-name-small">{{ $attribution->user->name ?? 'Utilisateur supprimé' }}</div>
                                    <div class="user-type-small">{{ $attribution->user->account_type ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="td-space">
                            <div class="space-compact">
                                <div class="space-icon-small">
                                    <i class="fas fa-building"></i>
                                    <div class="status-dot {{ $attribution->espace && $attribution->espace->is_active ? 'active' : 'inactive' }}"></div>
                                </div>
                                <div class="space-info-compact">
                                    <div class="space-name-small">{{ $attribution->espace->nom ?? 'Espace supprimé' }}</div>
                                    <div class="space-type-small">{{ $attribution->espace->space_type ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="td-period">
                            @if($attribution->start_date || $attribution->end_date)
                                <div class="period-compact">
                                    @if($attribution->start_date && $attribution->end_date)
                                        <div class="period-text">{{ $attribution->start_date->format('d/m/y') }} → {{ $attribution->end_date->format('d/m/y') }}</div>
                                    @elseif($attribution->start_date)
                                        <div class="period-text">Depuis {{ $attribution->start_date->format('d/m/y') }}</div>
                                    @elseif($attribution->end_date)
                                        <div class="period-text">Jusqu'au {{ $attribution->end_date->format('d/m/y') }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        
                        <div class="td-status">
                            <span class="status-indicator-small {{ $attribution->status }}">
                                @if($attribution->status === 'active')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($attribution->status === 'pending')
                                    <i class="fas fa-hourglass-half"></i>
                                @else
                                    <i class="fas fa-times-circle"></i>
                                @endif
                            </span>
                        </div>
                        
                        <div class="td-actions">
                            <div class="actions-compact">
                                <button class="action-btn-small view" onclick="viewAttribution({{ $attribution->id }})" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn-small edit" onclick="editAttribution({{ $attribution->id }})" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn-small delete" onclick="deleteAttribution({{ $attribution->id }}, '{{ $attribution->user->name ?? '' }}', '{{ $attribution->espace->nom ?? '' }}')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Vue en cartes (cachée par défaut) -->
            <div class="attributions-grid" id="attributionsGrid" style="display: none;">
                @foreach($attributions as $attribution)
                <div class="task-card" 
                     data-user-id="{{ $attribution->user_id }}" 
                     data-espace-id="{{ $attribution->espace_id }}"
                     data-user-name="{{ strtolower($attribution->user->name ?? '') }}"
                     data-espace-name="{{ strtolower($attribution->espace->nom ?? '') }}"
                     data-type="{{ $attribution->type }}">
                    
                    <div class="task-header">
                        <div class="task-type-badge {{ $attribution->type }}">
                            @if($attribution->type === 'permanente')
                                <i class="fas fa-infinity"></i>
                                <span>Permanente</span>
                            @elseif($attribution->type === 'ponctuelle')
                                <i class="fas fa-calendar-day"></i>
                                <span>Ponctuelle</span>
                            @else
                                <i class="fas fa-clock"></i>
                                <span>Temporaire</span>
                            @endif
                        </div>
                        
                        <div class="task-status {{ $attribution->status }}">
                            @if($attribution->status === 'active')
                                <i class="fas fa-check-circle"></i>
                            @elseif($attribution->status === 'pending')
                                <i class="fas fa-hourglass-half"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                        </div>
                    </div>

                    <div class="task-content">
                        <!-- Utilisateur -->
                        <div class="task-user">
                            <div class="user-avatar">
                                @if($attribution->user && $attribution->user->hasProfilePhoto())
                                    <img src="{{ $attribution->user->profile_photo_url }}" alt="{{ $attribution->user->name }}">
                                @elseif($attribution->user)
                                    <div class="avatar-initials">{{ strtoupper(substr($attribution->user->name, 0, 1)) }}</div>
                                @else
                                    <div class="avatar-initials">?</div>
                                @endif
                                <div class="status-indicator {{ $attribution->user && $attribution->user->is_active ? 'active' : 'inactive' }}"></div>
                            </div>
                            <div class="user-details">
                                <h3 class="user-name">{{ $attribution->user->name ?? 'Utilisateur supprimé' }}</h3>
                                @if($attribution->user)
                                    <p class="user-role">{{ $attribution->user->account_type }}</p>
                                    <p class="user-email">{{ $attribution->user->display_email }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Flèche de connexion -->
                        <div class="task-connection">
                            <i class="fas fa-arrow-right"></i>
                        </div>

                        <!-- Espace -->
                        <div class="task-space">
                            <div class="space-icon">
                                <i class="fas fa-building"></i>
                                <div class="status-indicator {{ $attribution->espace && $attribution->espace->is_active ? 'active' : 'inactive' }}"></div>
                            </div>
                            <div class="space-details">
                                <h3 class="space-name">{{ $attribution->espace->nom ?? 'Espace supprimé' }}</h3>
                                @if($attribution->espace)
                                    <p class="space-type">{{ $attribution->espace->space_type }}</p>
                                    <p class="space-email">{{ $attribution->espace->display_email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations de la tâche -->
                    <div class="task-info">
                        @if($attribution->start_date || $attribution->end_date)
                            <div class="task-period">
                                <i class="fas fa-calendar-alt"></i>
                                <span>
                                    @if($attribution->start_date && $attribution->end_date)
                                        {{ $attribution->start_date->format('d/m/Y') }} → {{ $attribution->end_date->format('d/m/Y') }}
                                    @elseif($attribution->start_date)
                                        Depuis {{ $attribution->start_date->format('d/m/Y') }}
                                    @elseif($attribution->end_date)
                                        Jusqu'au {{ $attribution->end_date->format('d/m/Y') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if($attribution->access_hours && count($attribution->access_hours) > 0)
                            <div class="task-hours">
                                <i class="fas fa-clock"></i>
                                <span>{{ count($attribution->access_hours) }} jours configurés</span>
                            </div>
                        @endif

                        @if($attribution->description)
                            <div class="task-description">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ Str::limit($attribution->description, 60) }}</span>
                            </div>
                        @endif
                        
                        <div class="task-meta">
                            <span class="task-date">
                                <i class="fas fa-calendar-plus"></i>
                                {{ $attribution->created_at ? $attribution->created_at->format('d/m/Y') : 'Date inconnue' }}
                            </span>
                            @if($attribution->created_at != $attribution->updated_at)
                                <span class="task-updated">
                                    <i class="fas fa-clock"></i>
                                    {{ $attribution->updated_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions de la tâche -->
                    <div class="task-actions">
                        <button class="task-action view" onclick="viewAttribution({{ $attribution->id }})" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="task-action edit" onclick="editAttribution({{ $attribution->id }})" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="task-action delete" onclick="deleteAttribution({{ $attribution->id }}, '{{ $attribution->user->name ?? '' }}', '{{ $attribution->espace->nom ?? '' }}')" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $attributions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-illustration">
                    <div class="empty-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="empty-connections">
                        <div class="connection-demo">
                            <div class="demo-user">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="demo-line"></div>
                            <div class="demo-space">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="empty-title">Aucune tâche configurée</h3>
                <p class="empty-description">
                    Commencez par créer des tâches d'accès aux utilisateurs pour organiser les permissions.
                </p>
                <button class="btn btn-primary btn-lg" onclick="showNewAttribution()">
                    <i class="fas fa-plus"></i>
                    Créer la première tâche
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour nouvelle attribution avec sélection d'espace -->
<div id="newAttributionModal" class="modal">
    <div class="modal-overlay" onclick="hideNewAttribution()"></div>
    <div class="modal-content attribution-modal">
        <div class="modal-header">
            <h3 id="modalTitle">
                <i class="fas fa-plus"></i>
                <span id="modalTitleText">Nouvelle Tâche</span>
            </h3>
            <button class="modal-close" onclick="hideNewAttribution()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Étape 1: Sélection d'espace -->
        <div class="modal-body" id="spaceSelectionStep">
            <div class="step-header">
                <h4>Choisissez un espace pour cette tâche</h4>
                <p>Sélectionnez l'espace qui sera attribué à l'utilisateur</p>
            </div>
            
            <div class="space-selection-grid">
                @forelse($espaces as $espace)
                    <div class="space-card" data-space-id="{{ $espace->id }}" onclick="selectSpaceForTask({{ $espace->id }}, '{{ $espace->nom }}')">
                        <div class="space-card-header">
                            <div class="space-card-icon">
                                <i class="fas fa-building"></i>
                                <div class="space-card-status {{ $espace->is_active ? 'active' : 'inactive' }}"></div>
                            </div>
                            <div class="space-card-info">
                                <h5 class="space-card-name">{{ $espace->nom }}</h5>
                                <span class="space-card-type">{{ $espace->space_type }}</span>
                            </div>
                        </div>
                        <div class="space-card-stats">
                            <div class="stat">
                                <i class="fas fa-users"></i>
                                <span>{{ $espace->users()->count() }} utilisateurs</span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $espace->display_email }}</span>
                            </div>
                        </div>
                        @if(!$espace->is_active)
                            <div class="space-card-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Espace inactif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="no-spaces">
                        <i class="fas fa-building"></i>
                        <p>Aucun espace disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Étape 2: Formulaire de tâche -->
        <div class="modal-body" id="taskFormStep" style="display: none;">
            <div class="step-header">
                <button class="back-btn" onclick="goBackToSpaceSelection()">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la sélection d'espace
                </button>
                <h4>Configuration de la tâche</h4>
                <div class="selected-space-info" id="selectedSpaceInfo">
                    <!-- Sera rempli dynamiquement -->
                </div>
            </div>
            
            <form id="newAttributionForm" class="attribution-form">
                @csrf
                <input type="hidden" id="attribution_id" name="attribution_id">
                <input type="hidden" id="form_method" name="_method" value="POST">
                
                <div class="form-grid">
                    <div class="form-group full-width" id="user_field">
                        <label for="user_id">
                            <i class="fas fa-user"></i>
                            Utilisateur
                        </label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="">Sélectionnez d'abord un espace</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width" id="espace_field">
                        <label for="espace_id">
                            <i class="fas fa-building"></i>
                            Espace
                        </label>
                        <select id="espace_id" name="espace_id" class="form-select" required>
                            <option value="">Sélectionner un espace</option>
                            @forelse($espaces as $espace)
                                <option value="{{ $espace->id }}" {{ !$espace->is_active ? 'disabled' : '' }}>
                                    {{ $espace->nom }}{{ !$espace->is_active ? ' [Inactif]' : '' }}
                                </option>
                            @empty
                                <option disabled>Aucun espace disponible</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Champs d'information en mode édition -->
                    <div class="form-group full-width" id="user_info" style="display: none;">
                        <label>
                            <i class="fas fa-user"></i>
                            Utilisateur
                        </label>
                        <div class="readonly-field">
                            <span id="user_display"></span>
                        </div>
                    </div>
                    
                    <div class="form-group full-width" id="espace_info" style="display: none;">
                        <label>
                            <i class="fas fa-building"></i>
                            Espace
                        </label>
                        <div class="readonly-field">
                            <span id="espace_display"></span>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="type">
                            <i class="fas fa-tag"></i>
                            Type de tâche
                        </label>
                        <select id="type" name="type" class="form-select" required>
                            <option value="">Sélectionner un type</option>
                            <option value="permanente">🏠 Tâche permanente</option>
                            <option value="ponctuelle">📅 Tâche ponctuelle</option>
                            <option value="temporaire">⏰ Tâche temporaire</option>
                        </select>
                    </div>

                    <div class="form-group half-width">
                        <label for="start_date">
                            <i class="fas fa-calendar-alt"></i>
                            Date de début
                        </label>
                        <input type="date" id="start_date" name="start_date" class="form-input">
                        <small class="form-help">Optionnel</small>
                    </div>
                    
                    <div class="form-group half-width">
                        <label for="end_date">
                            <i class="fas fa-calendar-alt"></i>
                            Date de fin
                        </label>
                        <input type="date" id="end_date" name="end_date" class="form-input">
                        <small class="form-help">Optionnel</small>
                    </div>

                    <div class="form-group full-width">
                        <label for="access_hours">
                            <i class="fas fa-clock"></i>
                            Fenêtres d'accès (optionnel)
                        </label>
                        <div class="access-hours-container">
                            <div class="access-hours-grid">
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="lundi">
                                        Lundi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="mardi">
                                        Mardi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="mercredi">
                                        Mercredi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="jeudi">
                                        Jeudi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="vendredi">
                                        Vendredi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="samedi">
                                        Samedi
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                                <div class="day-access">
                                    <label class="day-label">
                                        <input type="checkbox" class="day-checkbox" data-day="dimanche">
                                        Dimanche
                                    </label>
                                    <div class="time-inputs" style="display: none;">
                                        <input type="time" class="time-start" placeholder="Début">
                                        <span>-</span>
                                        <input type="time" class="time-end" placeholder="Fin">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-preset" onclick="setBusinessHours()">
                                <i class="fas fa-business-time"></i>
                                Horaires bureau (9h-17h)
                            </button>
                        </div>
                        <input type="hidden" id="access_hours_json" name="access_hours">
                        <small class="form-help">Définissez les plages horaires d'accès pour chaque jour de la semaine</small>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">
                            <i class="fas fa-edit"></i>
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3" class="form-textarea" 
                                  placeholder="Décrivez le motif de cette tâche..."></textarea>
                        <small class="form-help">Exemple: Accès pour projet du 15/02 au 30/06</small>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideNewAttribution()">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="button" class="btn btn-primary" id="submitButton" onclick="submitNewAttribution()">
                <i class="fas fa-plus"></i>
                <span id="submitText">Créer la tâche</span>
            </button>
        </div>
    </div>
</div>


<!-- Modal pour voir les détails d'une attribution -->
<div id="viewAttributionModal" class="modal">
    <div class="modal-overlay" onclick="hideViewAttribution()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-eye"></i>
                Détails de la Tâche
            </h3>
            <button class="modal-close" onclick="hideViewAttribution()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="attributionDetailsContent">
            <!-- Le contenu sera rempli dynamiquement -->
            <div class="loading-spinner" id="loadingSpinner">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Chargement des détails...</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideViewAttribution()">Fermer</button>
        </div>
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les fenêtres d'accès
    initializeAccessHours();
    
    // Recherche et filtrage
    const searchInput = document.getElementById('searchAttributions');
    const typeFilter = document.getElementById('typeFilter');
    let currentView = 'list';
    let currentEspaceFilterValue = ''; // Initialiser la variable pour le filtre d'espace
    const attributionCards = document.querySelectorAll('.task-card');
    const taskRows = document.querySelectorAll('.task-row');
    
    function filterAttributions() {
        // Vérifier que les éléments existent
        if (!searchInput || !typeFilter) {
            console.warn('Éléments de filtre manquants');
            return;
        }
        
        const searchTerm = searchInput.value.toLowerCase();
        const espaceFilterValue = currentEspaceFilterValue;
        const typeFilterValue = typeFilter.value;
        
        console.log('Filtrage avec:', { searchTerm, espaceFilterValue, typeFilterValue });
        
        const items = currentView === 'list' ? taskRows : attributionCards;
        let visibleCount = 0;
        
        items.forEach(item => {
            const userName = item.dataset.userName || '';
            const espaceName = item.dataset.espaceName || '';
            const espaceId = item.dataset.espaceId || '';
            const type = item.dataset.type || '';
            
            const matchesSearch = !searchTerm || userName.includes(searchTerm) || espaceName.includes(searchTerm);
            const matchesEspace = !espaceFilterValue || espaceId === espaceFilterValue;
            const matchesType = !typeFilterValue || type === typeFilterValue;
            
            if (matchesSearch && matchesEspace && matchesType) {
                item.style.display = currentView === 'list' ? 'flex' : 'block';
                item.style.animation = 'fadeInUp 0.3s ease';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        console.log(`${visibleCount} tâches visibles sur ${items.length}`);
        
        // Afficher un message si aucun résultat
        updateNoResultsMessage(visibleCount === 0);
    }
    
    function updateNoResultsMessage(show) {
        let noResultsMsg = document.getElementById('noResultsMessage');
        
        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noResultsMessage';
            noResultsMsg.className = 'no-results-message';
            noResultsMsg.innerHTML = `
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3>Aucune tâche trouvée</h3>
                    <p>Essayez de modifier vos critères de recherche</p>
                </div>
            `;
            
            const container = currentView === 'list' 
                ? document.querySelector('.tasks-table')
                : document.querySelector('.attributions-grid');
            
            if (container) {
                container.appendChild(noResultsMsg);
            }
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }
    
    // Fonction pour effacer tous les filtres
    function clearAllFilters() {
        if (searchInput) searchInput.value = '';
        if (typeFilter) typeFilter.value = '';
        
        // Réinitialiser le filtre d'espace
        currentEspaceFilterValue = '';
        const spaceFilterText = document.getElementById('spaceFilterText');
        if (spaceFilterText) {
            spaceFilterText.textContent = 'Tous les espaces';
        }
        
        // Réinitialiser les classes actives des espaces
        document.querySelectorAll('.space-filter-item').forEach(item => {
            item.classList.remove('active');
        });
        
        const allSpacesItem = document.querySelector('.space-filter-item[data-space-id=""]');
        if (allSpacesItem) {
            allSpacesItem.classList.add('active');
        }
        
        // Appliquer les filtres (maintenant vides)
        filterAttributions();
        
        // Notification
        showNotification('✨ Tous les filtres ont été effacés', 'success');
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterAttributions);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterAttributions);
    }
    
    // Gestion du filtrage par espace amélioré
    const spaceFilterToggle = document.getElementById('spaceFilterToggle');
    const spaceFilterDropdown = document.getElementById('spaceFilterDropdown');
    const spaceSearch = document.getElementById('spaceSearch');
    
    if (spaceFilterToggle) {
        spaceFilterToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            spaceFilterDropdown.classList.toggle('show');
        });
    }
    
    if (spaceSearch) {
        spaceSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const spaceItems = document.querySelectorAll('.space-filter-item');
            
            spaceItems.forEach(item => {
                const spaceName = item.dataset.spaceName || '';
                if (spaceName.includes(searchTerm) || !searchTerm) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function() {
        spaceFilterDropdown.classList.remove('show');
    });
    
    spaceFilterDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Gestion du redimensionnement pour les modales
    window.addEventListener('resize', function() {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            if (window.innerWidth <= 768) {
                modal.scrollTop = 0;
            }
        });
    });
    
    // Empêcher le scroll du body quand une modale est ouverte
    document.addEventListener('touchmove', function(e) {
        if (document.body.style.overflow === 'hidden') {
            const modal = document.querySelector('.modal.show');
            if (modal && !modal.contains(e.target)) {
                e.preventDefault();
            }
        }
    }, { passive: false });
});

// Variables globales pour la sélection d'espace
let selectedSpaceId = null;
let selectedSpaceName = null;


// Fonction pour sélectionner un espace dans le filtre
function selectSpace(spaceId, spaceName) {
    currentEspaceFilterValue = spaceId;
    
    // Mettre à jour l'affichage du bouton
    document.getElementById('spaceFilterText').textContent = spaceName;
    
    // Mettre à jour les classes actives
    document.querySelectorAll('.space-filter-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const targetItem = document.querySelector(`.space-filter-item[data-space-id="${spaceId}"]`);
    if (targetItem) {
        targetItem.classList.add('active');
    }
    
    // Fermer le dropdown
    document.getElementById('spaceFilterDropdown').classList.remove('show');
    
    // Appliquer le filtre
    filterAttributions();
}

// Fonction pour sélectionner un espace pour une nouvelle tâche
function selectSpaceForTask(spaceId, spaceName) {
    selectedSpaceId = spaceId;
    selectedSpaceName = spaceName;
    
    // Passer à l'étape suivante
    document.getElementById('spaceSelectionStep').style.display = 'none';
    document.getElementById('taskFormStep').style.display = 'block';
    
    // Mettre à jour le titre modal
    document.getElementById('modalTitleText').textContent = `Nouvelle Tâche - ${spaceName}`;
    
    // Afficher les informations de l'espace sélectionné
    const selectedSpaceInfo = document.getElementById('selectedSpaceInfo');
    if (selectedSpaceInfo) {
        selectedSpaceInfo.innerHTML = `
            <div class="selected-space-badge">
                <div class="space-icon-small">
                    <i class="fas fa-building"></i>
                </div>
                <div class="space-details">
                    <span class="space-name">${spaceName}</span>
                    <span class="space-label">Espace sélectionné</span>
                </div>
            </div>
        `;
    }
    
    // Pré-remplir le champ espace dans le formulaire et le cacher car il est pré-sélectionné
    const espaceSelect = document.getElementById('espace_id');
    const espaceField = document.getElementById('espace_field');
    if (espaceSelect) {
        espaceSelect.value = spaceId;
        espaceSelect.disabled = true; // Désactiver la modification
        // Cacher le champ espace puisqu'il est déjà sélectionné à l'étape précédente
        if (espaceField) {
            espaceField.style.display = 'none';
        }
    }

    // Charger les utilisateurs pour cet espace
    loadUsersForSpace(spaceId);
}

// Fonction pour charger les utilisateurs associés à un espace
async function loadUsersForSpace(espaceId) {
    const userSelect = document.getElementById('user_id');
    
    if (!userSelect) {
        console.error('Select des utilisateurs non trouvé');
        return;
    }

    // Afficher un indicateur de chargement
    userSelect.innerHTML = '<option value="">Chargement des utilisateurs...</option>';
    userSelect.disabled = true;

    try {
        const response = await fetch(`/admin/api/users/by-space/${espaceId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error('Erreur lors du chargement des utilisateurs');
        }

        const data = await response.json();

        if (data.success) {
            // Vider le select
            userSelect.innerHTML = '<option value="">Sélectionner un utilisateur</option>';

            // Ajouter les utilisateurs existants dans cet espace
            if (data.users.existing.length > 0) {
                const existingGroup = document.createElement('optgroup');
                existingGroup.label = 'Utilisateurs déjà dans cet espace';
                data.users.existing.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    existingGroup.appendChild(option);
                });
                userSelect.appendChild(existingGroup);
            }

            // Ajouter les autres utilisateurs disponibles
            if (data.users.available.length > 0) {
                const availableGroup = document.createElement('optgroup');
                availableGroup.label = 'Autres utilisateurs disponibles';
                data.users.available.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.email})`;
                    availableGroup.appendChild(option);
                });
                userSelect.appendChild(availableGroup);
            }

            // Si aucun utilisateur n'est disponible
            if (data.users.existing.length === 0 && data.users.available.length === 0) {
                userSelect.innerHTML = '<option value="" disabled>Aucun utilisateur disponible - Créez d\'abord des utilisateurs</option>';
            }
        } else {
            throw new Error(data.error || 'Erreur inconnue');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des utilisateurs:', error);
        userSelect.innerHTML = '<option value="" disabled>Erreur lors du chargement des utilisateurs</option>';
        
        // Afficher un message d'erreur à l'utilisateur
        if (typeof showNotification === 'function') {
            showNotification('Erreur lors du chargement des utilisateurs pour cet espace', 'error');
        }
    } finally {
        // Réactiver le select
        userSelect.disabled = false;
    }
}

// Fonction pour revenir à la sélection d'espace
function goBackToSpaceSelection() {
    document.getElementById('taskFormStep').style.display = 'none';
    document.getElementById('spaceSelectionStep').style.display = 'block';
    
    // Réinitialiser le titre
    document.getElementById('modalTitleText').textContent = 'Nouvelle Tâche';
    
    // Réinitialiser la sélection
    selectedSpaceId = null;
    selectedSpaceName = null;
    
    // Réactiver et ré-afficher le champ espace
    const espaceSelect = document.getElementById('espace_id');
    const espaceField = document.getElementById('espace_field');
    if (espaceSelect) {
        espaceSelect.disabled = false;
        espaceSelect.value = '';
        // Re-montrer le champ espace
        if (espaceField) {
            espaceField.style.display = 'block';
        }
    }

    // Réinitialiser le dropdown des utilisateurs
    const userSelect = document.getElementById('user_id');
    if (userSelect) {
        userSelect.innerHTML = '<option value="">Sélectionnez d\'abord un espace</option>';
        userSelect.disabled = false;
    }
}

// Fonction pour basculer entre les vues
function toggleView(view) {
    currentView = view;
    const listView = document.getElementById('tasksListView');
    const cardView = document.getElementById('attributionsGrid');
    const listBtn = document.getElementById('listView');
    const cardBtn = document.getElementById('cardView');
    
    if (view === 'list') {
        listView.style.display = 'block';
        cardView.style.display = 'none';
        listBtn.classList.add('active');
        cardBtn.classList.remove('active');
    } else {
        listView.style.display = 'none';
        cardView.style.display = 'grid';
        listBtn.classList.remove('active');
        cardBtn.classList.add('active');
    }
    
    // Réappliquer les filtres
    filterAttributions();
}

// Fonctions pour gérer les fenêtres d'accès
function initializeAccessHours() {
    // Écouteurs pour les checkboxes des jours
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const timeInputs = this.closest('.day-access').querySelector('.time-inputs');
            if (this.checked) {
                timeInputs.style.display = 'flex';
                // Définir des valeurs par défaut
                const startInput = timeInputs.querySelector('.time-start');
                const endInput = timeInputs.querySelector('.time-end');
                if (!startInput.value) startInput.value = '09:00';
                if (!endInput.value) endInput.value = '17:00';
            } else {
                timeInputs.style.display = 'none';
            }
            updateAccessHoursJSON();
        });
    });
    
    
    // Écouteurs pour les changements d'heure
    document.querySelectorAll('.time-start, .time-end').forEach(input => {
        input.addEventListener('change', updateAccessHoursJSON);
    });
    
}

function updateAccessHoursJSON() {
    const accessHours = {};
    document.querySelectorAll('.day-access').forEach(dayElement => {
        const checkbox = dayElement.querySelector('.day-checkbox');
        const day = checkbox.dataset.day;
        
        if (checkbox.checked) {
            const startTime = dayElement.querySelector('.time-start').value;
            const endTime = dayElement.querySelector('.time-end').value;
            
            if (startTime && endTime) {
                accessHours[day] = {
                    start: startTime,
                    end: endTime
                };
            }
        }
    });
    
    document.getElementById('access_hours_json').value = JSON.stringify(accessHours);
}


function setBusinessHours() {
    const weekdays = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
    
    // Décocher tous les jours d'abord
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('.day-access').querySelector('.time-inputs').style.display = 'none';
    });
    
    // Cocher et configurer les jours de semaine
    weekdays.forEach(day => {
        const checkbox = document.querySelector(`[data-day="${day}"]`);
        if (checkbox) {
            checkbox.checked = true;
            const timeInputs = checkbox.closest('.day-access').querySelector('.time-inputs');
            timeInputs.style.display = 'flex';
            timeInputs.querySelector('.time-start').value = '09:00';
            timeInputs.querySelector('.time-end').value = '17:00';
        }
    });
    
    updateAccessHoursJSON();
}


// Fonctions pour les modals
function showNewAttribution() {
    resetFormToCreateMode();
    
    // Commencer par l'étape de sélection d'espace
    document.getElementById('spaceSelectionStep').style.display = 'block';
    document.getElementById('taskFormStep').style.display = 'none';
    document.getElementById('modalTitleText').textContent = 'Nouvelle Tâche';
    
    const modal = document.getElementById('newAttributionModal');
    modal.style.display = 'flex';
    // Force reflow before adding class
    modal.offsetHeight;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Scroll to top on mobile devices
    if (window.innerWidth <= 768) {
        modal.scrollTop = 0;
    }
}

function editAttribution(attributionId) {
    // Récupérer les données de l'attribution
    fetch(`{{ url('admin/attributions') }}/${attributionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setFormToEditMode(data.attribution);
                showEditAttribution();
            } else {
                alert('Erreur lors du chargement des données de la tâche');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des données de l\'attribution');
        });
}

function showEditAttribution() {
    // Pour l'édition, aller directement au formulaire
    const spaceStep = document.getElementById('spaceSelectionStep');
    const taskStep = document.getElementById('taskFormStep');
    
    if (spaceStep && taskStep) {
        spaceStep.style.display = 'none';
        taskStep.style.display = 'block';
    }
    
    const modal = document.getElementById('newAttributionModal');
    modal.style.display = 'flex';
    modal.offsetHeight;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    if (window.innerWidth <= 768) {
        modal.scrollTop = 0;
    }
}

function resetFormToCreateMode() {
    // Reset form title
    const modalTitle = document.getElementById('modalTitleText');
    if (modalTitle) {
        modalTitle.textContent = 'Nouvelle Tâche';
    }
    
    // Reset form fields
    const attributionId = document.getElementById('attribution_id');
    const formMethod = document.getElementById('form_method');
    if (attributionId) attributionId.value = '';
    if (formMethod) formMethod.value = 'POST';
    
    // Show user and espace selects if they exist
    const userField = document.getElementById('user_field');
    const espaceField = document.getElementById('espace_field');
    const userInfo = document.getElementById('user_info');
    const espaceInfo = document.getElementById('espace_info');
    
    if (userField) userField.style.display = 'block';
    if (espaceField) espaceField.style.display = 'block';
    if (userInfo) userInfo.style.display = 'none';
    if (espaceInfo) espaceInfo.style.display = 'none';
    
    // Reset required attributes
    const userId = document.getElementById('user_id');
    const espaceId = document.getElementById('espace_id');
    if (userId) userId.required = true;
    if (espaceId) espaceId.required = true;
    
    // Reset submit button
    const submitButton = document.getElementById('submitButton');
    if (submitButton) {
        submitButton.innerHTML = '<i class="fas fa-plus"></i> <span id="submitText">Créer la tâche</span>';
    }
    
    // Reset variables
    selectedSpaceId = null;
    selectedSpaceName = null;
}

function setFormToEditMode(attribution) {
    // Set form title
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier la Tâche';
    
    // Set form data
    document.getElementById('attribution_id').value = attribution.id;
    document.getElementById('form_method').value = 'PUT';
    
    // Hide user select, show readonly info for user only
    document.getElementById('user_field').style.display = 'none';
    document.getElementById('user_info').style.display = 'block';
    
    // Hide espace select, show readonly info for espace too
    document.getElementById('espace_field').style.display = 'none';
    document.getElementById('espace_info').style.display = 'block';
    
    // Set required attributes
    document.getElementById('user_id').required = false;
    document.getElementById('espace_id').required = false;
    
    // Set readonly info for user and espace
    document.getElementById('user_display').textContent = `${attribution.user.name} (${attribution.user.email})`;
    document.getElementById('espace_display').textContent = `${attribution.espace.nom} (${attribution.espace.space_type || ''})`;
    
    // Set current values for form fields (hidden, but needed for form submission)
    document.getElementById('espace_id').value = attribution.espace.id;
    
    // Fill form fields
    document.getElementById('type').value = attribution.type;
    document.getElementById('description').value = attribution.description || '';
    document.getElementById('start_date').value = attribution.start_date ? convertDateToInput(attribution.start_date) : '';
    document.getElementById('end_date').value = attribution.end_date ? convertDateToInput(attribution.end_date) : '';
    
    // Fill access hours
    if (attribution.access_hours && Object.keys(attribution.access_hours).length > 0) {
        fillAccessHours(attribution.access_hours);
    }
    
    // Update submit button
    document.getElementById('submitButton').innerHTML = '<i class="fas fa-save"></i> <span id="submitText">Modifier la tâche</span>';
}

function convertDateToInput(dateString) {
    // Convert from d/m/Y to Y-m-d format for input[type="date"]
    const parts = dateString.split('/');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    return '';
}

function fillAccessHours(accessHours) {
    // Reset all checkboxes and time inputs
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('.day-access').querySelector('.time-inputs').style.display = 'none';
    });
    
    // Fill the access hours
    Object.entries(accessHours).forEach(([day, hours]) => {
        const checkbox = document.querySelector(`[data-day="${day}"]`);
        if (checkbox) {
            checkbox.checked = true;
            const timeInputs = checkbox.closest('.day-access').querySelector('.time-inputs');
            timeInputs.style.display = 'flex';
            timeInputs.querySelector('.time-start').value = hours.start;
            timeInputs.querySelector('.time-end').value = hours.end;
        }
    });
    
    updateAccessHoursJSON();
}

function hideNewAttribution() {
    const modal = document.getElementById('newAttributionModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form if it exists
        const form = document.getElementById('newAttributionForm');
        if (form) {
            form.reset();
        }
        
        // Reset access hours
        const accessHoursField = document.getElementById('access_hours_json');
        if (accessHoursField) {
            accessHoursField.value = '';
        }
        
        // Reset access hours display
        document.querySelectorAll('.day-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            const timeInputs = checkbox.closest('.day-access')?.querySelector('.time-inputs');
            if (timeInputs) {
                timeInputs.style.display = 'none';
            }
        });
        
        // Reset to first step
        const spaceStep = document.getElementById('spaceSelectionStep');
        const taskStep = document.getElementById('taskFormStep');
        if (spaceStep && taskStep) {
            spaceStep.style.display = 'block';
            taskStep.style.display = 'none';
        }
        
        // Reset variables
        selectedSpaceId = null;
        selectedSpaceName = null;
        
    }, 300);
}


function submitNewAttribution() {
    const form = document.getElementById('newAttributionForm');
    const formData = new FormData(form);
    const submitButton = document.querySelector('.modal-footer .btn-primary');
    
    const attributionId = formData.get('attribution_id');
    const method = formData.get('_method');
    const isEditMode = attributionId && method === 'PUT';
    
    // Validation côté client
    const type = formData.get('type');
    const startDate = formData.get('start_date');
    const endDate = formData.get('end_date');
    
    if (!type) {
        alert('Veuillez sélectionner un type de tâche.');
        return;
    }
    
    if (!isEditMode) {
        const userId = formData.get('user_id');
        const espaceId = selectedSpaceId || formData.get('espace_id');
        
        if (!userId || !espaceId) {
            alert('Veuillez sélectionner un utilisateur et un espace.');
            return;
        }
    }
    
    if (startDate && endDate && startDate > endDate) {
        alert('La date de fin doit être postérieure à la date de début.');
        return;
    }
    
    // Désactiver le bouton et montrer le loading
    const originalText = submitButton.innerHTML;
    const loadingText = isEditMode ? 
        '<i class="fas fa-spinner fa-spin"></i> Modification...' : 
        '<i class="fas fa-spinner fa-spin"></i> Création...';
    submitButton.innerHTML = loadingText;
    submitButton.disabled = true;
    
    // Préparer les données
    let requestData = {
        type: type,
        description: formData.get('description') || '',
        start_date: startDate || null,
        end_date: endDate || null,
        access_hours: formData.get('access_hours') || null
    };
    
    if (!isEditMode) {
        requestData.user_id = formData.get('user_id');
        requestData.espace_id = selectedSpaceId || formData.get('espace_id');
    } else {
        // En mode édition, on peut modifier l'espace mais pas l'utilisateur
        requestData.espace_id = formData.get('espace_id');
    }
    
    // Déterminer l'URL et la méthode
    const url = isEditMode ? 
        `{{ url('admin/attributions') }}/${attributionId}` : 
        '{{ route("admin.attributions.store") }}';
    const fetchMethod = isEditMode ? 'PUT' : 'POST';
    
    // Envoyer la requête AJAX
    fetch(url, {
        method: fetchMethod,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideNewAttribution();
            const successMessage = isEditMode ? 
                (data.message || 'Tâche modifiée avec succès !') : 
                (data.message || 'Tâche créée avec succès !');
            showSuccessMessage(successMessage);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            const errorMessage = isEditMode ? 
                (data.error || 'Erreur lors de la modification de la tâche') : 
                (data.error || 'Erreur lors de la création de la tâche');
            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert(error.message);
    })
    .finally(() => {
        // Réactiver le bouton
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}


function viewAttribution(attributionId) {
    const modal = document.getElementById('viewAttributionModal');
    const content = document.getElementById('attributionDetailsContent');
    const spinner = document.getElementById('loadingSpinner');
    
    // Afficher la modale avec le spinner
    modal.style.display = 'flex';
    modal.offsetHeight;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Afficher le spinner
    spinner.style.display = 'flex';
    
    // Récupérer les détails de l'attribution
    fetch(`{{ url('admin/attributions') }}/${attributionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAttributionDetails(data.attribution);
            } else {
                throw new Error('Erreur lors du chargement des détails');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            content.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Erreur lors du chargement des détails de la tâche</span>
                </div>
            `;
        });
}

function hideViewAttribution() {
    const modal = document.getElementById('viewAttributionModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

function displayAttributionDetails(attribution) {
    const content = document.getElementById('attributionDetailsContent');
    
    const statusClass = `status-${attribution.status}`;
    const userStatus = attribution.user.is_active ? 'actif' : 'inactif';
    const espaceStatus = attribution.espace.is_active ? 'actif' : 'inactif';
    
    content.innerHTML = `
        <div class="attribution-details-view">
            <!-- En-tête avec type et statut -->
            <div class="details-header">
                <div class="type-badge" style="background-color: ${attribution.type_color}20; color: ${attribution.type_color}; border-color: ${attribution.type_color}40;">
                    ${attribution.type_display}
                </div>
                <div class="status-badge ${statusClass}">
                    ${attribution.status_display}
                </div>
                ${attribution.task_status ? `
                    <div class="task-status-badge task-status-${attribution.task_status}">
                        👤 ${attribution.task_status_display}
                    </div>
                ` : ''}
            </div>

            <!-- Informations principales -->
            <div class="details-section">
                <h4>👤 Utilisateur</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Nom :</span>
                        <span class="value">${attribution.user.name}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email :</span>
                        <span class="value">${attribution.user.email}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Type :</span>
                        <span class="value">${attribution.user.account_type}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Statut :</span>
                        <span class="value ${attribution.user.is_active ? 'text-success' : 'text-warning'}">${userStatus}</span>
                    </div>
                </div>
            </div>

            <div class="details-section">
                <h4>🏢 Espace</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Nom :</span>
                        <span class="value">${attribution.espace.nom}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email :</span>
                        <span class="value">${attribution.espace.email}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Type :</span>
                        <span class="value">${attribution.espace.space_type}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Statut :</span>
                        <span class="value ${attribution.espace.is_active ? 'text-success' : 'text-warning'}">${espaceStatus}</span>
                    </div>
                </div>
            </div>

            ${attribution.description ? `
                <div class="details-section">
                    <h4>📝 Description</h4>
                    <div class="description-box">
                        ${attribution.description}
                    </div>
                </div>
            ` : ''}

            ${attribution.access_hours && Object.keys(attribution.access_hours).length > 0 ? `
                <div class="details-section">
                    <h4>🕒 Fenêtres d'accès</h4>
                    <div class="access-hours-details">
                        ${Object.entries(attribution.access_hours).map(([day, hours]) => `
                            <div class="access-day-detail">
                                <span class="day-name">${day.charAt(0).toUpperCase() + day.slice(1)}</span>
                                <span class="day-hours">${hours.start} - ${hours.end}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}

            ${attribution.start_date || attribution.end_date ? `
                <div class="details-section">
                    <h4>📅 Période de validité</h4>
                    <div class="info-grid">
                        ${attribution.start_date ? `
                            <div class="info-item">
                                <span class="label">Date de début :</span>
                                <span class="value">${attribution.start_date}</span>
                            </div>
                        ` : ''}
                        ${attribution.end_date ? `
                            <div class="info-item">
                                <span class="label">Date de fin :</span>
                                <span class="value">${attribution.end_date}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            ` : ''}
            
            ${attribution.task_status ? `
                <!-- Informations de tâche (Worker) -->
                <div class="details-section">
                    <h4>📋 État de la Tâche (Travailleur)</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Statut de tâche :</span>
                            <span class="value task-status-value task-status-${attribution.task_status}">${attribution.task_status_display}</span>
                        </div>
                        ${attribution.completed_at ? `
                            <div class="info-item">
                                <span class="label">Terminée le :</span>
                                <span class="value">${attribution.completed_at}</span>
                            </div>
                        ` : ''}
                        <div class="info-item">
                            <span class="label">Progression :</span>
                            <span class="value">
                                ${attribution.task_status === 'completed' ? '✅ Terminée' : 
                                  attribution.task_status === 'active' ? '🔄 En cours' : 
                                  attribution.task_status === 'paused' ? '⏸️ En pause' : 
                                  '⏳ En attente'}
                            </span>
                        </div>
                    </div>
                </div>
            ` : ''}

            <!-- Informations système -->
            <div class="details-section">
                <h4>ℹ️ Informations système</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">ID Attribution :</span>
                        <span class="value">#${attribution.id}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Créée le :</span>
                        <span class="value">${attribution.created_at}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Modifiée le :</span>
                        <span class="value">${attribution.updated_at}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function deleteAttribution(attributionId, userName, espaceName) {
    if (confirm(`Êtes-vous sûr de vouloir dissocier ${userName} de l'espace ${espaceName} ?`)) {
        fetch(`{{ url('admin/attributions') }}/${attributionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message || 'Attribution supprimée avec succès !');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.error || 'Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert(error.message || 'Erreur lors de la suppression de la tâche');
        });
    }
}

function showSuccessMessage(message) {
    // Créer un élément de notification
    const notification = document.createElement('div');
    notification.className = 'success-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Ajouter les styles inline si nécessaire
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animer l'apparition
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Gestion des paramètres de session pour les nouveaux utilisateurs
document.addEventListener('DOMContentLoaded', function() {
    @if(session('highlight_user'))
        // Mettre en évidence l'utilisateur nouvellement créé avec attribution
        const userId = {{ session('highlight_user') }};
        highlightNewUserWithAttribution(userId);
    @elseif(session('suggest_user'))
        // Suggérer la création d'une attribution pour le nouvel utilisateur
        const userId = {{ session('suggest_user') }};
        suggestNewAttribution(userId);
    @endif
});

function highlightNewUserWithAttribution(userId) {
    // Trouver les tâches de cet utilisateur et les mettre en évidence
    const userRows = document.querySelectorAll(`[data-user-id="${userId}"]`);
    userRows.forEach(row => {
        row.style.background = 'linear-gradient(135deg, #dcfce7, #bbf7d0)';
        row.style.border = '2px solid #22c55e';
        row.style.boxShadow = '0 4px 12px rgba(34, 197, 94, 0.3)';
        
        // Ajouter une animation de pulsation
        row.classList.add('pulse-success');
        
        // Scroll vers le premier élément trouvé
        if (row === userRows[0]) {
            setTimeout(() => {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 500);
        }
    });
    
    // Supprimer la mise en évidence après 5 secondes
    setTimeout(() => {
        userRows.forEach(row => {
            row.style.background = '';
            row.style.border = '';
            row.style.boxShadow = '';
            row.classList.remove('pulse-success');
        });
    }, 5000);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const colors = {
        success: { bg: '#dcfce7', border: '#22c55e', text: '#166534' },
        info: { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af' },
        warning: { bg: '#fef3c7', border: '#f59e0b', text: '#92400e' },
        error: { bg: '#fef2f2', border: '#ef4444', text: '#dc2626' }
    };
    
    const color = colors[type] || colors.success;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${color.bg};
        color: ${color.text};
        border: 2px solid ${color.border};
        border-radius: 8px;
        padding: 16px 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 400px;
        font-weight: 500;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animer l'apparition
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Supprimer après 4 secondes
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

function suggestNewAttribution(userId) {
    // Afficher une notification suggérant de créer une attribution
    showNotification('💡 Voulez-vous créer une attribution pour cet utilisateur ?', 'info');
    
    // Après 2 secondes, proposer d'ouvrir le modal de création
    setTimeout(() => {
        if (confirm('Voulez-vous créer une nouvelle attribution pour cet utilisateur ?')) {
            showNewAttribution();
            // Pré-sélectionner l'utilisateur dans le formulaire
            setTimeout(() => {
                const userSelect = document.getElementById('user_id');
                if (userSelect) {
                    userSelect.value = userId;
                }
            }, 200);
        }
    }, 2000);
}
</script>

<!-- Styles CSS pour la page des tâches -->
<style>
/* Nouvelle structure des filtres */
.section-header {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 24px;
    margin-bottom: 24px;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}


.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
}

.section-title i {
    color: #0f766e;
    font-size: 22px;
}

.filters-container {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.filters-row {
    display: flex;
    gap: 24px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.search-filter {
    flex: 1;
    min-width: 300px;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #64748b;
    font-size: 14px;
    z-index: 2;
}

.search-box input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #0f766e;
    background: white;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
}

.filter-group {
    display: flex;
    gap: 16px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 160px;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.filter-select {
    padding: 10px 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 160px;
}

.filter-select:focus {
    outline: none;
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
}

.filter-select:hover {
    border-color: #cbd5e1;
}

.view-controls {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 16px;
}

.btn-new-task {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #0f766e, #134e4a);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(15, 118, 110, 0.2);
}

.btn-new-task:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
    background: linear-gradient(135deg, #0d9488, #115e59);
}

.btn-new-task:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(15, 118, 110, 0.2);
}

.btn-new-task i {
    font-size: 12px;
}

.view-toggle {
    display: flex;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 2px;
    gap: 2px;
}

.view-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: transparent;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-btn:hover {
    color: #0f766e;
    background: rgba(15, 118, 110, 0.1);
}

.view-btn.active {
    background: white;
    color: #0f766e;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.view-btn i {
    font-size: 12px;
}

.view-btn span {
    font-size: 12px;
}
/* Filtre d'espace amélioré */
.space-filter-container {
    position: relative;
}

.space-filter-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    color: #374151;
    min-width: 200px;
    justify-content: space-between;
}

.space-filter-toggle:hover {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.space-filter-toggle i:first-child {
    color: #0d9488;
}

.space-filter-toggle i:last-child {
    font-size: 12px;
    color: #9ca3af;
    transition: transform 0.2s ease;
}

.space-filter-toggle.active i:last-child {
    transform: rotate(180deg);
}

.space-filter-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-height: 400px;
    overflow: hidden;
    display: none;
}

.space-filter-dropdown.show {
    display: block;
    animation: fadeInDown 0.2s ease;
}

@keyframes fadeInDown {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.space-filter-header {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.space-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s ease;
}

.space-search-input:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.space-filter-list {
    max-height: 320px;
    overflow-y: auto;
}

.space-filter-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #f8fafc;
}

.space-filter-item:hover {
    background: #f8fafc;
}

.space-filter-item.active {
    background: rgba(13, 148, 136, 0.1);
    border-left: 3px solid #0d9488;
}

.space-item-icon {
    position: relative;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.space-item-icon.all-spaces {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
}

.space-status-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.space-status-dot.active {
    background: #10b981;
}

.space-status-dot.inactive {
    background: #ef4444;
}

.space-item-info {
    flex: 1;
    min-width: 0;
}

.space-item-name {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.space-item-type {
    font-size: 11px;
    color: #0d9488;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}

.space-item-count {
    font-size: 12px;
    color: #6b7280;
}

.space-filter-empty {
    padding: 20px;
    text-align: center;
    color: #9ca3af;
    font-style: italic;
}

/* Sélection d'espace pour nouvelle tâche */
.step-header {
    text-align: center;
    margin-bottom: 24px;
}

.step-header h4 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.step-header p {
    color: #6b7280;
    margin: 0;
}

.space-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    max-height: 400px;
    overflow-y: auto;
    padding: 4px;
}

.space-card {
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.space-card:hover {
    border-color: #0d9488;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(13, 148, 136, 0.15);
}

.space-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.space-card-icon {
    position: relative;
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
}

.space-card-status {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}

.space-card-status.active {
    background: #10b981;
}

.space-card-status.inactive {
    background: #ef4444;
}

.space-card-info {
    flex: 1;
}

.space-card-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.space-card-type {
    font-size: 12px;
    color: #0d9488;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.space-card-stats {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
}

.stat i {
    color: #9ca3af;
    width: 14px;
}

.space-card-warning {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.no-spaces {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

.no-spaces i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.back-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    color: #0d9488;
    font-size: 14px;
    cursor: pointer;
    padding: 8px 0;
    margin-bottom: 16px;
    transition: color 0.2s ease;
}

.back-btn:hover {
    color: #115e59;
}

.selected-space-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(13, 148, 136, 0.1);
    border: 1px solid rgba(13, 148, 136, 0.2);
    border-radius: 8px;
    padding: 12px;
    margin-top: 16px;
}

.selected-space-badge .space-icon-small {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.space-details {
    flex: 1;
}

.space-name {
    font-size: 14px;
    font-weight: 600;
    color: #0d9488;
    display: block;
}

.space-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Toggle de vue */
.view-toggle {
    display: flex;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 4px;
    gap: 2px;
}

.view-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-btn.active {
    background: white;
    color: #0d9488;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.view-btn:hover:not(.active) {
    color: #374151;
    background: rgba(255, 255, 255, 0.5);
}

/* Vue liste compacte */
.tasks-list-view {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #f1f5f9;
}

.tasks-table {
    display: flex;
    flex-direction: column;
}

.table-header {
    display: flex;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    font-size: 12px;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 0;
}

.table-header > div {
    display: flex;
    align-items: center;
    padding: 0 16px;
}

.th-type { flex: 0 0 120px; }
.th-user { flex: 1; min-width: 200px; }
.th-space { flex: 1; min-width: 180px; }
.th-period { flex: 0 0 140px; }
.th-status { flex: 0 0 80px; justify-content: center; }
.th-actions { flex: 0 0 120px; justify-content: center; }

.task-row {
    display: flex;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    min-height: 60px;
}

.task-row:hover {
    background: #fafbfc;
    transform: translateX(2px);
}

.task-row:last-child {
    border-bottom: none;
}

.task-row > div {
    display: flex;
    align-items: center;
    padding: 12px 16px;
}

.td-type { flex: 0 0 120px; }
.td-user { flex: 1; min-width: 200px; }
.td-space { flex: 1; min-width: 180px; }
.td-period { flex: 0 0 140px; }
.td-status { flex: 0 0 80px; justify-content: center; }
.td-actions { flex: 0 0 120px; justify-content: center; }

/* Badges de type compacts */
.type-badge-small {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: capitalize;
}

.type-badge-small.permanente {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.type-badge-small.ponctuelle {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.type-badge-small.temporaire {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
}

/* Utilisateur compact */
.user-compact, .space-compact {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
}

.user-avatar-small {
    position: relative;
    flex-shrink: 0;
}

.user-avatar-small img, .avatar-initials-small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: cover;
}

.avatar-initials-small {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.space-icon-small {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    position: relative;
    flex-shrink: 0;
}

.status-dot {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-dot.active {
    background: #10b981;
}

.status-dot.inactive {
    background: #ef4444;
}

.user-info-compact, .space-info-compact {
    flex: 1;
    min-width: 0;
}

.user-name-small, .space-name-small {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 2px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-type-small, .space-type-small {
    font-size: 11px;
    color: #0d9488;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0;
}

.period-compact {
    font-size: 12px;
}

.period-text {
    color: #374151;
    font-weight: 500;
}

.text-muted {
    color: #9ca3af;
    font-size: 14px;
}

.status-indicator-small {
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-indicator-small.active {
    color: #10b981;
}

.status-indicator-small.pending {
    color: #f59e0b;
}

.status-indicator-small.expired {
    color: #ef4444;
}

.actions-compact {
    display: flex;
    gap: 4px;
}

.action-btn-small {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 12px;
}

.action-btn-small.view {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.action-btn-small.view:hover {
    background: rgba(59, 130, 246, 0.2);
}

.action-btn-small.edit {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.action-btn-small.edit:hover {
    background: rgba(245, 158, 11, 0.2);
}

.action-btn-small.delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.action-btn-small.delete:hover {
    background: rgba(239, 68, 68, 0.2);
}

/* Grid principal pour vue cartes */
.attributions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

/* Cartes de tâches modernes */
.task-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.task-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: #0d9488;
}

/* Header avec badges */
.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
}

.task-type-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.task-type-badge.permanente {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.task-type-badge.ponctuelle {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.task-type-badge.temporaire {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.task-status {
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.task-status.active {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.task-status.pending {
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
}

.task-status.expired {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

/* Contenu principal */
.task-content {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
}

.task-user, .task-space {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    position: relative;
    flex-shrink: 0;
}

.user-avatar img, .avatar-initials {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    object-fit: cover;
}

.avatar-initials {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
}

.space-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    position: relative;
    flex-shrink: 0;
}

.status-indicator {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-indicator.active {
    background: #10b981;
}

.status-indicator.inactive {
    background: #ef4444;
}

.user-details, .space-details {
    flex: 1;
    min-width: 0;
}

.user-name, .space-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-role, .space-type {
    font-size: 12px;
    color: #0d9488;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 4px 0;
}

.user-email, .space-email {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    font-family: 'Courier New', monospace;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.task-connection {
    color: #94a3b8;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 50%;
    flex-shrink: 0;
}

/* Informations de la tâche */
.task-info {
    padding: 16px 20px;
    background: #fafbfc;
    border-top: 1px solid #f1f5f9;
}

.task-period, .task-hours, .task-description {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    color: #64748b;
}

.task-period i, .task-hours i, .task-description i {
    color: #0d9488;
    width: 16px;
    text-align: center;
}

.task-meta {
    display: flex;
    gap: 16px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
    font-size: 12px;
    color: #94a3b8;
}

.task-date, .task-updated {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Actions de la tâche */
.task-actions {
    display: flex;
    gap: 8px;
    padding: 16px 20px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    justify-content: center;
}

.task-action {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.task-action.view {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.task-action.view:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-1px);
}

.task-action.edit {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.task-action.edit:hover {
    background: rgba(245, 158, 11, 0.2);
    transform: translateY(-1px);
}

.task-action.delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.task-action.delete:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
}

/* Design responsive */
@media (max-width: 768px) {
    .header-top {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .filters-row {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .search-filter {
        min-width: unset;
    }
    
    .filter-group {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .filter-item {
        min-width: unset;
    }
    
    .view-controls {
        justify-content: center;
        margin-top: 16px;
        flex-direction: column;
        gap: 12px;
    }
    
    .btn-new-task {
        width: 100%;
        justify-content: center;
        order: -1;
    }
    
    .space-filter-toggle {
        min-width: auto;
        width: 100%;
    }
    
    .space-filter-dropdown {
        left: 0;
        right: 0;
    }
}
    
    /* Vue liste responsive */
    .table-header {
        display: none;
    }
    
    .task-row {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
        min-height: auto;
        gap: 12px;
    }
    
    .task-row > div {
        padding: 0;
        flex: none;
    }
    
    .td-type {
        order: 1;
    }
    
    .td-user {
        order: 2;
    }
    
    .td-space {
        order: 3;
    }
    
    .td-period {
        order: 4;
    }
    
    .td-status {
        order: 5;
        justify-content: flex-start;
    }
    
    .td-actions {
        order: 6;
        justify-content: flex-start;
    }
    
    /* Vue cartes responsive */
    .attributions-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .task-content {
        flex-direction: column;
        gap: 16px;
        padding: 16px;
    }
    
    .task-user, .task-space {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
    
    .task-connection {
        transform: rotate(90deg);
        order: 1;
    }
    
    .task-user {
        order: 0;
    }
    
    .task-space {
        order: 2;
    }
    
    .task-header {
        padding: 12px 16px;
    }
    
    .task-info {
        padding: 12px 16px;
    }
    
    .task-actions {
        padding: 12px 16px;
    }
}

@media (max-width: 480px) {
    .view-btn {
        padding: 6px 8px;
        font-size: 12px;
    }
    
    .view-btn i {
        display: none;
    }
    
    /* Vue liste très petits écrans */
    .user-compact, .space-compact {
        gap: 8px;
    }
    
    .user-avatar-small img, .avatar-initials-small, .space-icon-small {
        width: 28px;
        height: 28px;
    }
    
    .user-name-small, .space-name-small {
        font-size: 13px;
    }
    
    .user-type-small, .space-type-small {
        font-size: 10px;
    }
    
    .action-btn-small {
        width: 32px;
        height: 32px;
    }
    
    /* Vue cartes très petits écrans */
    .attributions-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .task-card {
        border-radius: 12px;
    }
    
    .task-type-badge {
        font-size: 10px;
        padding: 4px 8px;
    }
    
    .task-status {
        width: 28px;
        height: 28px;
        font-size: 16px;
    }
    
    .user-avatar img, .avatar-initials, .space-icon {
        width: 40px;
        height: 40px;
    }
    
    .avatar-initials {
        font-size: 16px;
    }
}


/* État vide */
.empty-state {
    text-align: center;
    padding: 60px 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 2px dashed #d1d5db;
}

.empty-illustration {
    margin-bottom: 24px;
}

.empty-icon i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.empty-title {
    font-size: 20px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 8px 0;
}

.empty-description {
    color: #6b7280;
    font-size: 14px;
    margin: 0 0 24px 0;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .attributions-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .attribution-connection {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .user-section, .espace-section {
        justify-content: center;
        max-width: 100%;
    }
    
    .connection-line {
        transform: rotate(90deg);
    }
    
    .card-header, .card-body, .card-footer {
        padding: 14px;
    }
}

@media (max-width: 480px) {
    .user-section, .espace-section {
        flex-direction: column;
        gap: 8px;
        align-items: center;
    }
    
    .attribution-connection {
        gap: 8px;
    }
}

/* Styles pour les modales */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    padding: 20px;
    box-sizing: border-box;
}

.modal.show {
    display: flex;
    opacity: 1;
    visibility: visible;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
    z-index: -1;
}

.modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 500px;
    max-height: 85vh;
    overflow: hidden;
    transform: scale(0.95);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
}

.modal.show .modal-content {
    transform: scale(1);
}

.attribution-modal {
    max-width: 600px;
    width: 95%;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    background: #f8fafc;
    border-radius: 12px 12px 0 0;
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 16px;
    color: #9ca3af;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #e5e7eb;
    color: #374151;
}

.modal-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    flex-shrink: 0;
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
}

/* Formulaire dans la modale */
.form-grid {
    display: grid;
    gap: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group.half-width {
    grid-column: span 1;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group label i {
    color: #22c55e;
    font-size: 12px;
}

.form-select, .form-input, .form-textarea {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    transition: all 0.2s ease;
    width: 100%;
    box-sizing: border-box;
}

.form-select:focus, .form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.form-textarea {
    resize: vertical;
    font-family: inherit;
    line-height: 1.5;
    min-height: 80px;
}

.form-help {
    font-size: 11px;
    color: #6b7280;
    margin-top: 2px;
    font-style: italic;
}

.readonly-field {
    padding: 10px 12px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #374151;
    font-weight: 500;
}

/* Styles pour les fenêtres d'accès */
.access-hours-container {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
}

.access-hours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}

.day-access {
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 8px;
    transition: all 0.2s ease;
}

.day-access:hover {
    border-color: #22c55e;
    background: rgba(34, 197, 94, 0.05);
}

.day-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    margin-bottom: 8px;
}

.day-checkbox {
    width: 16px;
    height: 16px;
    accent-color: #22c55e;
}

.time-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}

.time-inputs span {
    color: #6b7280;
    font-weight: 500;
}

.time-start, .time-end {
    flex: 1;
    padding: 6px 8px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 12px;
    background: white;
    transition: all 0.2s ease;
}

.time-start:focus, .time-end:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
}

.btn-preset {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-preset:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

/* Animation pour les nouveaux utilisateurs */
@keyframes pulse-success {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.pulse-success {
    animation: pulse-success 1s ease-in-out infinite;
}

/* Message aucun résultat */
.no-results-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px dashed #e2e8f0;
    margin: 20px 0;
}

.no-results-content {
    color: #64748b;
}

.no-results-content i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.no-results-content h3 {
    font-size: 18px;
    font-weight: 600;
    color: #475569;
    margin: 0 0 8px 0;
}

.no-results-content p {
    margin: 0;
    font-size: 14px;
}

/* Bouton d'effacement des filtres */
.btn-clear-filters {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-size: 14px;
    padding: 8px 12px;
    transition: all 0.2s ease;
}

.btn-clear-filters:hover {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}

@media (max-width: 768px) {
    .hide-mobile {
        display: none;
    }
    
    .btn-clear-filters {
        padding: 8px;
    }
}

/* Styles pour les tâches terminées dans Admin */
.task-status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid;
    background: white;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.task-status-badge.task-status-pending {
    color: #f59e0b;
    border-color: #f59e0b;
    background: #fef3c7;
}

.task-status-badge.task-status-active {
    color: #10b981;
    border-color: #10b981;
    background: #d1fae5;
}

.task-status-badge.task-status-paused {
    color: #f97316;
    border-color: #f97316;
    background: #fed7aa;
}

.task-status-badge.task-status-completed {
    color: #6b7280;
    border-color: #6b7280;
    background: #f3f4f6;
}

.task-status-value.task-status-pending {
    color: #f59e0b;
    font-weight: 600;
}

.task-status-value.task-status-active {
    color: #10b981;
    font-weight: 600;
}

.task-status-value.task-status-paused {
    color: #f97316;
    font-weight: 600;
}

.task-status-value.task-status-completed {
    color: #6b7280;
    font-weight: 600;
}

.details-header {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
    align-items: center;
}

.details-header .task-status-badge {
    font-size: 11px;
    padding: 3px 8px;
}
</style>
@endsection