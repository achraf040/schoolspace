@extends('admin.layout')

@section('title', 'Détails Espace')
@section('page-title', 'Détails de l\'Espace')

@section('content')
<div class="espace-detail-container">
    <!-- En-tête du profil -->
    <div class="espace-header">
        <div class="espace-avatar">
            <i class="fas fa-building"></i>
        </div>
        <div class="espace-info">
            <h1 class="espace-name">{{ $espace->nom }}</h1>
            <p class="espace-email">{{ $espace->email }}</p>
            <div class="espace-status">
                <span class="status-badge {{ $espace->is_active ? 'status-active' : 'status-inactive' }}">
                    <i class="fas fa-{{ $espace->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                    {{ $espace->is_active ? 'Espace actif' : 'Espace inactif' }}
                </span>
            </div>
        </div>
        <div class="espace-actions">
            <a href="{{ route('admin.espaces.edit', $espace) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route('admin.espaces.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="espace-content">
        <div class="content-grid">
            <!-- Carte informations -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informations de l'espace
                    </h3>
                </div>
                <div class="card-body">
                    @if($espace->description)
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-align-left info-icon"></i>
                            <div class="info-content">
                                <label>Description</label>
                                <span>{{ $espace->description }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-calendar-plus info-icon"></i>
                            <div class="info-content">
                                <label>Créé le</label>
                                <span>{{ $espace->created_at ? $espace->created_at->format('d/m/Y à H:i') : 'Non défini' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-item">
                            <i class="fas fa-clock info-icon"></i>
                            <div class="info-content">
                                <label>Dernière modification</label>
                                <span>{{ $espace->updated_at ? $espace->updated_at->format('d/m/Y à H:i') : 'Non défini' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte utilisateurs attribués -->
            <div class="info-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i>
                        Utilisateurs attribués
                        <span class="badge-count">{{ $espace->users->count() }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($espace->users->count() > 0)
                        <div class="users-list">
                            @foreach($espace->users as $user)
                            <div class="user-item">
                                <div class="user-avatar">
                                    @if($user->hasProfilePhoto())
                                        <img src="{{ $user->profile_photo_url }}" alt="Photo de {{ $user->name }}">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email {{ !$user->email ? 'missing-email' : '' }}" 
                                         title="{{ $user->email ? 'Cliquer pour copier: ' . $user->email : 'Email non défini' }}"
                                         @if($user->email) onclick="copyToClipboard('{{ $user->email }}', this)" @endif>
                                        @if($user->email)
                                            <i class="fas fa-envelope"></i>
                                            <span class="email-text">{{ $user->email }}</span>
                                        @else
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span class="email-text">Email non défini</span>
                                        @endif
                                    </div>
                                    <div class="user-meta">
                                        Attribué le {{ $user->pivot->created_at ? $user->pivot->created_at->format('d/m/Y') : 'Date inconnue' }}
                                    </div>
                                </div>
                                <div class="user-status">
                                    <span class="user-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <h4>Aucun utilisateur attribué</h4>
                            <p>Cet espace n'a aucun utilisateur attribué pour le moment.</p>
                            <a href="{{ route('admin.attributions.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Attribuer des utilisateurs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Conteneur principal */
.espace-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* En-tête de l'espace */
.espace-header {
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

.espace-avatar {
    flex-shrink: 0;
}

.espace-avatar i {
    font-size: 80px;
    color: rgba(255, 255, 255, 0.9);
}

.espace-info {
    flex: 1;
}

.espace-name {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
}

.espace-email {
    font-size: 18px;
    opacity: 0.9;
    margin: 0 0 16px 0;
}

.espace-status .status-badge {
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

.espace-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Contenu principal */
.espace-content {
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

/* Informations de l'espace */
.info-row {
    margin-bottom: 20px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-item {
    display: flex;
    align-items: flex-start;
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
    margin-top: 2px;
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
    line-height: 1.5;
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
    padding: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.user-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #0f766e;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    overflow: hidden;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.user-info {
    flex: 1;
    min-width: 0; /* Permet au flex item de se rétrécir plus que sa taille de contenu */
    overflow: hidden;
}

.user-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 16px;
    margin-bottom: 4px;
}

.user-email {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    line-height: 1.4;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    background: transparent;
    padding: 0;
    border: none;
    transition: all 0.2s ease;
}

.user-email:hover {
    color: #475569;
    background: #f1f5f9;
    padding: 4px 8px;
    border-radius: 6px;
    cursor: pointer;
}

.user-email.missing-email {
    color: #dc2626;
    font-style: italic;
}

.user-email.missing-email:hover {
    background: #fef2f2;
}

.user-email i {
    font-size: 12px;
    opacity: 0.8;
    flex-shrink: 0;
    width: 12px;
    text-align: center;
}

.user-email .email-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
}

.user-meta {
    color: #94a3b8;
    font-size: 12px;
}

.user-status {
    flex-shrink: 0;
}

.user-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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
    
    .espace-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .espace-actions {
        flex-direction: row;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .espace-detail-container {
        padding: 0 16px;
    }
    
    .espace-header {
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .espace-name {
        font-size: 24px;
    }
    
    .espace-email {
        font-size: 16px;
    }
    
    .espace-avatar i {
        font-size: 60px;
    }
    
    .user-item {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .user-info {
        text-align: center;
    }
    
    .user-email {
        font-size: 11px;
        justify-content: center;
        gap: 4px;
    }
    
    .user-email:hover {
        padding: 2px 6px;
    }
}
</style>

<script>
function copyToClipboard(text, element) {
    // Créer un élément temporaire pour la copie
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Pour mobile
    
    try {
        // Copier le texte
        document.execCommand('copy');
        
        // Feedback visuel
        const originalBg = element.style.background;
        const originalColor = element.style.color;
        
        element.style.background = '#dcfce7';
        element.style.color = '#166534';
        element.style.padding = '4px 8px';
        element.style.borderRadius = '6px';
        
        // Changer temporairement l'icône
        const icon = element.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'fas fa-check';
        
        // Restaurer après 1.5 secondes
        setTimeout(() => {
            element.style.background = originalBg;
            element.style.color = originalColor;
            element.style.padding = '';
            element.style.borderRadius = '';
            icon.className = originalClass;
        }, 1500);
        
        // Notification toast (optionnel)
        showCopyNotification('Email copié dans le presse-papiers !');
        
    } catch (err) {
        console.error('Erreur lors de la copie:', err);
        showCopyNotification('Erreur lors de la copie', 'error');
    }
    
    // Supprimer l'élément temporaire
    document.body.removeChild(tempInput);
}

function showCopyNotification(message, type = 'success') {
    // Créer la notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#dcfce7' : '#fef2f2'};
        color: ${type === 'success' ? '#166534' : '#dc2626'};
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        font-size: 14px;
        font-weight: 500;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'times'}" style="margin-right: 8px;"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animation de sortie
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection