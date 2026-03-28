@extends('admin.layout')

@section('title', 'Détails Utilisateur')
@section('page-title', 'Profil Utilisateur')

@section('content')
<div class="user-profile-container">
    <!-- En-tête du profil -->
    <div class="profile-header">
        <div class="profile-avatar">
            @if($user->hasProfilePhoto())
                <img src="{{ $user->profile_photo_url }}" alt="Photo de {{ $user->name }}">
            @else
                <i class="fas fa-user-circle"></i>
            @endif
        </div>
        <div class="profile-info">
            <h1 class="profile-name">{{ $user->name }}</h1>
            <p class="profile-email">{{ $user->display_email }}</p>
            <div class="profile-status">
                <span class="status-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                    <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'ban' }}"></i>
                    {{ $user->is_active ? 'Compte actif' : 'Compte inactif' }}
                </span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="profile-content">
        <div class="content-grid">
            <!-- Carte informations personnelles -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card"></i>
                        Informations personnelles
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-user info-icon"></i>
                            <div class="info-content">
                                <label>Nom complet</label>
                                <span>{{ $user->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-envelope info-icon"></i>
                            <div class="info-content">
                                <label>Adresse email</label>
                                <span>{{ $user->display_email }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-calendar-plus info-icon"></i>
                            <div class="info-content">
                                <label>Membre depuis</label>
                                <span>{{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : 'Non défini' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-clock info-icon"></i>
                            <div class="info-content">
                                <label>Dernière modification</label>
                                <span>{{ $user->updated_at ? $user->updated_at->format('d/m/Y à H:i') : 'Non défini' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte espaces attribués -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i>
                        Espaces attribués
                        <span class="badge-count">{{ $user->espaces->count() }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($user->espaces->count() > 0)
                        <div class="spaces-list">
                            @foreach($user->espaces as $espace)
                            <div class="space-item">
                                <div class="space-header">
                                    <div class="space-name">
                                        <i class="fas fa-door-open"></i>
                                        {{ $espace->nom }}
                                    </div>
                                    <span class="space-status {{ $espace->is_active ? 'status-active' : 'status-inactive' }}">
                                        {{ $espace->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                                @if($espace->description)
                                    <p class="space-description">{{ $espace->description }}</p>
                                @endif
                                <div class="space-meta">
                                    <i class="fas fa-calendar-alt"></i>
                                    Attribué le {{ $espace->pivot->created_at ? $espace->pivot->created_at->format('d/m/Y') : 'Date inconnue' }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-building-slash"></i>
                            <h4>Aucun espace attribué</h4>
                            <p>Cet utilisateur n'a aucun espace attribué pour le moment.</p>
                            <a href="{{ route('admin.attributions.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Attribuer des espaces
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="action-bar">
        <div class="quick-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Modifier le profil
            </a>
            
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i>
                    Supprimer
                </button>
            </form>
        </div>
        
        <div class="secondary-actions">
            <a href="{{ route('admin.attributions.index') }}" class="btn btn-outline">
                <i class="fas fa-link"></i>
                Gérer les attributions
            </a>
        </div>
    </div>
</div>

<style>
/* Conteneur principal */
.user-profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* En-tête du profil */
.profile-header {
    background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    color: white;
    box-shadow: 0 10px 25px rgba(15, 118, 110, 0.2);
}

.profile-avatar {
    flex-shrink: 0;
}

.profile-avatar i {
    font-size: 80px;
    color: rgba(255, 255, 255, 0.9);
}

.profile-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
}

.profile-email {
    font-size: 18px;
    opacity: 0.9;
    margin: 0 0 16px 0;
}

.profile-status .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
}

.status-active {
    background: rgba(34, 197, 94, 0.2);
    color: #16a34a;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.status-inactive {
    background: rgba(251, 146, 60, 0.2);
    color: #ea580c;
    border: 1px solid rgba(251, 146, 60, 0.3);
}

.profile-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Contenu principal */
.profile-content {
    margin-bottom: 32px;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
}

/* Cartes d'information */
.info-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: #f9fafb;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.card-title i {
    color: #0f766e;
    font-size: 20px;
}

.badge-count {
    background: #0f766e;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-left: auto;
}

.card-body {
    padding: 24px;
}

/* Informations utilisateur */
.info-row {
    margin-bottom: 20px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.info-icon {
    color: #0f766e;
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.info-content {
    flex: 1;
}

.info-content label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.info-content span {
    font-size: 16px;
    color: #1e293b;
    font-weight: 500;
}

/* Liste des espaces */
.spaces-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.space-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s ease;
}

.space-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.space-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.space-name {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #1e293b;
    font-size: 16px;
}

.space-name i {
    color: #0f766e;
}

.space-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.space-description {
    color: #64748b;
    font-size: 14px;
    margin: 0 0 12px 0;
    line-height: 1.5;
}

.space-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #94a3b8;
}

.space-meta i {
    color: #0f766e;
}

/* État vide */
.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: #64748b;
}

.empty-state i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.empty-state h4 {
    font-size: 18px;
    font-weight: 600;
    color: #475569;
    margin: 0 0 8px 0;
}

.empty-state p {
    margin: 0 0 20px 0;
    font-size: 14px;
}

/* Barre d'actions */
.action-bar {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.quick-actions {
    display: flex;
    gap: 12px;
}

.secondary-actions {
    display: flex;
    gap: 12px;
}

.delete-form {
    display: inline;
}

/* Boutons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #0f766e;
    color: white;
}

.btn-primary:hover {
    background: #134e4a;
    transform: translateY(-1px);
}

.btn-outline {
    background: white;
    color: #0f766e;
    border: 1px solid #0f766e;
}

.btn-outline:hover {
    background: #0f766e;
    color: white;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
}

/* Responsive */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .profile-actions {
        flex-direction: row;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .user-profile-container {
        padding: 0 16px;
    }
    
    .profile-header {
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .profile-name {
        font-size: 24px;
    }
    
    .profile-email {
        font-size: 16px;
    }
    
    .profile-avatar i {
        font-size: 60px;
    }
    
    .action-bar {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .quick-actions,
    .secondary-actions {
        justify-content: center;
    }
}
</style>
@endsection