@extends('admin.layout')

@section('title', 'Gestion des Espaces')
@section('page-title', 'Gestion des Espaces')

@section('content')
<div class="content-section">
    <!-- En-tête avec statistiques et actions -->
    <div class="espaces-header">
        <div class="header-main">
            <div class="header-info">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    Espaces de Travail
                </h2>
                <p class="section-subtitle">Gérez les espaces et leurs configurations</p>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $espaces->total() }}</div>
                        <div class="stat-label">Total Espaces</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $espaces->where('is_active', true)->count() }}</div>
                        <div class="stat-label">Actifs</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $espaces->sum('users_count') }}</div>
                        <div class="stat-label">Utilisateurs Total</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions et filtres -->
        <div class="header-actions">
            <div class="search-filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchEspaces" placeholder="Rechercher un espace...">
                </div>
                <select id="statusFilter" class="status-filter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Espaces actifs</option>
                    <option value="inactive">Espaces inactifs</option>
                </select>
            </div>
            <a href="{{ route('admin.espaces.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouvel Espace
            </a>
        </div>
    </div>

    <div class="form-container">
        @if($espaces->count() > 0)
            <!-- Vue en cartes améliorée -->
            <div class="espaces-grid" id="espacesGrid">
                @foreach($espaces as $espace)
                <div class="espace-card" data-status="{{ $espace->is_active ? 'active' : 'inactive' }}" data-name="{{ strtolower($espace->nom) }}">
                    <div class="card-header">
                        <div class="espace-avatar">
                            <div class="avatar-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="status-indicator {{ $espace->is_active ? 'active' : 'inactive' }}"></div>
                        </div>
                        
                        <div class="espace-info">
                            <h3 class="espace-name">{{ $espace->nom }}</h3>
                            <div class="espace-email">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $espace->email }}</span>
                            </div>
                            @if($espace->description)
                                <p class="espace-description">{{ Str::limit($espace->description, 80) }}</p>
                            @endif
                        </div>
                        
                        <div class="espace-status">
                            <span class="status-badge {{ $espace->is_active ? 'active' : 'inactive' }}">
                                <i class="fas fa-{{ $espace->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                {{ $espace->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Statistiques détaillées -->
                        <div class="espace-stats">
                            <div class="stat-item">
                                <div class="stat-icon-small">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">{{ $espace->users_count }}</span>
                                    <span class="stat-label">Utilisateurs</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon-small">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">{{ $espace->created_at ? $espace->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    <span class="stat-label">Créé le</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon-small">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">{{ $espace->updated_at->diffForHumans() }}</span>
                                    <span class="stat-label">Mis à jour</span>
                                </div>
                            </div>
                        </div>

                        <!-- Barre de progression si des utilisateurs -->
                        @if($espace->users_count > 0)
                            <div class="usage-indicator">
                                <div class="usage-label">
                                    <span>Utilisation</span>
                                    <span class="usage-count">{{ $espace->users_count }} utilisateur(s)</span>
                                </div>
                                <div class="usage-bar">
                                    <div class="usage-fill" style="width: {{ min(($espace->users_count / max($espaces->max('users_count'), 1)) * 100, 100) }}%;"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="espace-actions">
                            <a href="{{ route('admin.espaces.show', $espace) }}" class="action-btn view" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                                <span>Voir</span>
                            </a>
                            <a href="{{ route('admin.espaces.edit', $espace) }}" class="action-btn edit" title="Modifier l'espace">
                                <i class="fas fa-edit"></i>
                                <span>Modifier</span>
                            </a>
                            @if($espace->users_count == 0)
                                <button class="action-btn delete" onclick="deleteEspace({{ $espace->id }}, '{{ $espace->nom }}')" title="Supprimer l'espace">
                                    <i class="fas fa-trash"></i>
                                    <span>Supprimer</span>
                                </button>
                            @else
                                <button class="action-btn disabled" title="Impossible de supprimer : utilisateurs assignés" disabled>
                                    <i class="fas fa-lock"></i>
                                    <span>Protégé</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $espaces->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-illustration">
                    <div class="empty-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="empty-buildings">
                        <div class="building building-1"></div>
                        <div class="building building-2"></div>
                        <div class="building building-3"></div>
                    </div>
                </div>
                <h3 class="empty-title">Aucun espace configuré</h3>
                <p class="empty-description">
                    Créez votre premier espace de travail pour commencer à organiser vos utilisateurs et leurs accès.
                </p>
                <a href="{{ route('admin.espaces.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i>
                    Créer le premier espace
                </a>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche et filtrage
    const searchInput = document.getElementById('searchEspaces');
    const statusFilter = document.getElementById('statusFilter');
    const espaceCards = document.querySelectorAll('.espace-card');
    
    function filterEspaces() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilterValue = statusFilter.value;
        
        espaceCards.forEach(card => {
            const name = card.dataset.name;
            const status = card.dataset.status;
            
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesStatus = !statusFilterValue || status === statusFilterValue;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEspaces);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterEspaces);
    }
});

function deleteEspace(espaceId, espaceName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'espace "${espaceName}" ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/espaces/${espaceId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Styles CSS modernisés -->
<style>
/* En-tête des espaces */
.espaces-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.espaces-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #10b981, #06b6d4, #8b5cf6);
    opacity: 0.8;
}

.header-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.header-info {
    flex: 1;
}

.section-subtitle {
    color: #6b7280;
    font-size: 16px;
    margin: 8px 0 0 0;
}

/* Statistiques rapides */
.quick-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    min-width: 160px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-icon.total {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.stat-icon.active {
    background: linear-gradient(135deg, #10b981, #059669);
}

.stat-icon.users {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

/* Actions et filtres */
.header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.search-filter-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #9ca3af;
    font-size: 14px;
}

.search-box input {
    padding: 10px 16px 10px 40px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    width: 250px;
    background: white;
    transition: all 0.2s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.status-filter {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.status-filter:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Grid des espaces */
.espaces-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* Cartes d'espaces */
.espace-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.espace-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border-color: rgba(16, 185, 129, 0.2);
}

.card-header {
    padding: 24px 24px 16px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    background: linear-gradient(135deg, #f9fafb 0%, white 100%);
    border-bottom: 1px solid #f3f4f6;
}

.espace-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.status-indicator {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.status-indicator.active {
    background: #10b981;
}

.status-indicator.inactive {
    background: #f59e0b;
}

.espace-info {
    flex: 1;
    min-width: 0;
}

.espace-name {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.espace-email {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #10b981;
    font-size: 14px;
    font-weight: 500;
    background: rgba(16, 185, 129, 0.1);
    padding: 6px 12px;
    border-radius: 20px;
    margin-bottom: 8px;
    border: 1px solid rgba(16, 185, 129, 0.2);
    display: inline-flex;
}

.espace-email i {
    font-size: 12px;
}

.espace-description {
    font-size: 14px;
    color: #6b7280;
    margin: 8px 0 0 0;
    line-height: 1.4;
}

.espace-status {
    flex-shrink: 0;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-badge.inactive {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.card-body {
    padding: 16px 24px;
}

.espace-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
}

.stat-icon-small {
    width: 32px;
    height: 32px;
    background: #10b981;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.stat-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.stat-details .stat-number {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1;
}

.stat-details .stat-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.usage-indicator {
    margin-top: 16px;
}

.usage-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.usage-count {
    font-weight: 600;
    color: #10b981;
}

.usage-bar {
    height: 6px;
    background: #f3f4f6;
    border-radius: 3px;
    overflow: hidden;
}

.usage-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 3px;
    transition: width 1s ease-in-out;
}

.card-footer {
    padding: 16px 24px 24px;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
}

.espace-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.action-btn.view {
    background: #e5e7eb;
    color: #374151;
}

.action-btn.view:hover {
    background: #d1d5db;
    transform: translateY(-1px);
}

.action-btn.edit {
    background: rgba(59, 130, 246, 0.1);
    color: #1d4ed8;
}

.action-btn.edit:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-1px);
}

.action-btn.delete {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.action-btn.delete:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
}

.action-btn.disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

/* État vide avec illustration */
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 2px dashed #d1d5db;
    position: relative;
}

.empty-illustration {
    position: relative;
    margin-bottom: 32px;
}

.empty-icon {
    margin-bottom: 20px;
}

.empty-icon i {
    font-size: 64px;
    color: #d1d5db;
}

.empty-buildings {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
}

.building {
    width: 12px;
    height: 20px;
    background: #e5e7eb;
    border-radius: 2px 2px 0 0;
    animation: buildingPulse 2s ease-in-out infinite;
}

.building-1 {
    height: 16px;
    animation-delay: 0s;
}

.building-2 {
    height: 24px;
    background: #d1d5db;
    animation-delay: 0.3s;
}

.building-3 {
    height: 18px;
    animation-delay: 0.6s;
}

@keyframes buildingPulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.empty-title {
    font-size: 24px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 12px 0;
}

.empty-description {
    color: #6b7280;
    font-size: 16px;
    margin: 0 0 32px 0;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 32px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .header-main {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .quick-stats {
        justify-content: space-between;
    }
    
    .stat-card {
        flex: 1;
        min-width: 140px;
    }
    
    .espaces-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    }
}

@media (max-width: 768px) {
    .espaces-header {
        padding: 24px;
    }
    
    .header-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .search-filter-group {
        flex-direction: column;
        gap: 12px;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .quick-stats {
        flex-direction: column;
    }
    
    .espaces-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .card-header {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .espace-stats {
        grid-template-columns: 1fr;
    }
    
    .espace-actions {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .espaces-header {
        padding: 16px;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .card-header,
    .card-body,
    .card-footer {
        padding: 16px;
    }
    
    .avatar-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.espace-card {
    animation: fadeInUp 0.3s ease;
}

.espace-card:nth-child(1) { animation-delay: 0.1s; }
.espace-card:nth-child(2) { animation-delay: 0.2s; }
.espace-card:nth-child(3) { animation-delay: 0.3s; }
.espace-card:nth-child(4) { animation-delay: 0.4s; }
.espace-card:nth-child(5) { animation-delay: 0.5s; }
.espace-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endsection