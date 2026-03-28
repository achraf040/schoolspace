@extends('admin.layout')

@section('title', 'Gestion des Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-users"></i>
            Liste des Utilisateurs
        </h2>
        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchUsers" placeholder="Rechercher un utilisateur...">
            </div>
            <select id="statusFilter" class="status-filter">
                <option value="">Tous les statuts</option>
                <option value="active">Actifs</option>
                <option value="inactive">Inactifs</option>
            </select>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Ajouter un utilisateur
            </a>
        </div>
    </div>

    <!-- Statistiques simples -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-label">Total:</span>
            <span class="stat-value">{{ $users->total() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Actifs:</span>
            <span class="stat-value text-success">{{ $users->where('is_active', true)->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Inactifs:</span>
            <span class="stat-value text-warning">{{ $users->where('is_active', false)->count() }}</span>
        </div>
    </div>

    <div class="form-container">
        @if($users->count() > 0)
            <div class="users-list" id="usersList">
                @foreach($users as $user)
                <div class="user-item" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                    <div class="user-avatar">
                        @if($user->hasProfilePhoto())
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="avatar-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif
                        <div class="status-dot {{ $user->is_active ? 'active' : 'inactive' }}"></div>
                    </div>
                    
                    <div class="user-details">
                        <div class="user-main">
                            <h4 class="user-name">{{ $user->name }}</h4>
                            <span class="user-email" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>
                        <div class="user-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-building"></i>
                                <span>{{ $user->espaces->count() }} espace(s)</span>
                            </div>
                        </div>
                        @if($user->espaces->count() > 0)
                            <div class="user-spaces">
                                @foreach($user->espaces->take(3) as $espace)
                                    <span class="space-badge" title="{{ $espace->nom }}">{{ $espace->nom }}</span>
                                @endforeach
                                @if($user->espaces->count() > 3)
                                    <span class="space-badge more">+{{ $user->espaces->count() - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="user-status">
                        <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-warning' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    
                    <div class="user-actions">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-secondary" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}" 
                                onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                                title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-users empty-icon"></i>
                <h3>Aucun utilisateur trouvé</h3>
                <p>Commencez par ajouter votre premier utilisateur.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Ajouter un utilisateur
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Scripts pour les fonctionnalités interactives -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche et filtrage
    const searchInput = document.getElementById('searchUsers');
    const statusFilter = document.getElementById('statusFilter');
    const userItems = document.querySelectorAll('.user-item');
    
    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilterValue = statusFilter.value;
        
        userItems.forEach(item => {
            const name = item.dataset.name;
            const email = item.dataset.email;
            const status = item.dataset.status;
            
            const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = !statusFilterValue || status === statusFilterValue;
            
            if (matchesSearch && matchesStatus) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterUsers);
    }
});

function toggleUserStatus(userId, newStatus) {
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ is_active: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            button.innerHTML = originalHTML;
            button.disabled = false;
            alert(data.error || 'Erreur lors de la modification du statut');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalHTML;
        button.disabled = false;
        alert('Erreur lors de la modification du statut');
    });
}

function deleteUser(userId, userName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur ${userName} ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Styles CSS simplifiés et bien alignés -->
<style>
/* En-tête avec actions */
.header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
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
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.status-filter {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.status-filter:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Barre de statistiques */
.stats-bar {
    display: flex;
    gap: 24px;
    margin-bottom: 24px;
    padding: 16px 24px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.stats-bar .stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.text-success {
    color: #059669 !important;
}

.text-warning {
    color: #d97706 !important;
}

/* Liste des utilisateurs */
.users-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.user-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.user-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

/* Avatar utilisateur */
.user-avatar {
    position: relative;
    flex-shrink: 0;
}

.user-avatar img,
.avatar-initials {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-initials {
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
}

.status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
}

.status-dot.active {
    background: #10b981;
}

.status-dot.inactive {
    background: #f59e0b;
}

/* Détails utilisateur */
.user-details {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    max-width: calc(100% - 200px); /* Réserve de l'espace pour l'avatar et les actions */
}

.user-main {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 6px;
}

.user-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.user-email {
    font-size: 14px;
    color: #6b7280;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 250px;
    font-family: 'Courier New', Monaco, monospace;
}

.user-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 8px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #6b7280;
}

.meta-item i {
    font-size: 11px;
}

/* Espaces utilisateur */
.user-spaces {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 8px;
    max-width: 100%;
    overflow: hidden;
}

.space-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid #dbeafe;
    white-space: nowrap;
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: all 0.2s ease;
}

.space-badge:hover {
    background: #dbeafe;
    transform: scale(1.05);
}

.space-badge.more {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #e5e7eb;
    font-weight: 600;
}

/* Statut utilisateur */
.user-status {
    flex-shrink: 0;
}

/* Actions utilisateur */
.user-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.btn-sm {
    padding: 8px 10px;
    font-size: 12px;
    line-height: 1;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-icon {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 18px;
    color: #374151;
    margin: 0 0 8px 0;
}

.empty-state p {
    margin: 0 0 24px 0;
    font-size: 14px;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 32px;
}

/* Responsive */
@media (max-width: 768px) {
    .header-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .search-box input {
        width: 100%;
    }
    
    .stats-bar {
        flex-direction: column;
        gap: 12px;
    }
    
    .user-item {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .user-main {
        flex-direction: column;
        gap: 4px;
    }
    
    .user-meta {
        justify-content: center;
    }
    
    .user-actions {
        justify-content: center;
    }
    
    .user-spaces {
        justify-content: center;
        max-width: 100%;
    }
    
    .space-badge {
        max-width: 80px;
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .user-item {
        padding: 16px;
    }
    
    .user-avatar img,
    .avatar-initials {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .status-dot {
        width: 12px;
        height: 12px;
        border-width: 1px;
    }
}
</style>
@endsection