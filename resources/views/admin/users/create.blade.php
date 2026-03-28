@extends('admin.layout')

@section('title', 'Ajouter un Utilisateur')
@section('page-title', 'Ajouter un Utilisateur')

@section('content')
<div class="modern-content">
    <!-- Header avec gradient -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="breadcrumb">
                    <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">
                        <i class="fas fa-users"></i>
                        Utilisateurs
                    </a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Nouveau</span>
                </div>
                <h1 class="page-title">
                    <i class="fas fa-user-plus"></i>
                    Créer un nouvel utilisateur
                </h1>
                <p class="page-subtitle">Ajoutez un nouveau membre à votre équipe avec les permissions appropriées</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="form-layout">
        @if($errors->has('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ $errors->first('error') }}</span>
            </div>
        @endif
        
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="modern-form">
            @csrf
            
            <!-- Section Photo de profil -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-image"></i>
                        Photo de profil
                    </h3>
                    <p class="section-subtitle">Ajoutez une photo pour personnaliser le profil</p>
                </div>
                
                <div class="photo-upload-modern">
                    <div class="photo-preview-modern" id="photoPreview">
                        <div class="preview-placeholder">
                            <i class="fas fa-user-circle"></i>
                            <span>Aucune photo</span>
                        </div>
                    </div>
                    <div class="photo-controls">
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                        <button type="button" class="btn-upload" onclick="document.getElementById('profile_photo').click()">
                            <i class="fas fa-camera"></i>
                            Choisir une photo
                        </button>
                        <button type="button" class="btn-remove" id="removePhotoBtn" onclick="removePhoto()" style="display: none;">
                            <i class="fas fa-trash-alt"></i>
                            Supprimer
                        </button>
                    </div>
                    <div class="photo-info">
                        <span><i class="fas fa-info-circle"></i> JPG, PNG, GIF • Max 2MB • 100x100 à 2000x2000px</span>
                    </div>
                    @error('profile_photo')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Section Informations personnelles -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Informations personnelles
                    </h3>
                    <p class="section-subtitle">Renseignez les informations de base de l'utilisateur</p>
                </div>
                
                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <i class="fas fa-id-card"></i>
                            Nom complet *
                        </label>
                        <input type="text" name="name" class="field-input" placeholder="Ex: Ahmed Ben Ali" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <i class="fas fa-envelope"></i>
                            Adresse email *
                        </label>
                        <input type="email" name="email" class="field-input" placeholder="nom.prenom@supmti.ac.ma" value="{{ old('email') }}" required>
                        <div class="field-help">
                            <i class="fas fa-info-circle"></i>
                            <span>Format : nom.prenom@supmti.ac.ma</span>
                            <button type="button" class="email-info-toggle" onclick="toggleEmailInfo()">
                                <i class="fas fa-eye-slash" id="emailToggleIcon"></i>
                                <span id="emailToggleText">Masquer les exemples</span>
                            </button>
                        </div>
                        <div class="email-prefix-info" id="emailPrefixInfo">
                            <div class="prefix-examples">
                                <div class="info-header">
                                    <h4>
                                        <i class="fas fa-magic"></i>
                                        Emails automatiques après attribution
                                    </h4>
                                    <p>L'email sera automatiquement modifié avec un préfixe lors de l'attribution à un espace</p>
                                </div>
                                <div class="prefix-list">
                                    <div class="prefix-item">
                                        <div class="prefix-badge scol">scol</div>
                                        <div class="prefix-content">
                                            <div class="prefix-example">scol.nom.prenom@supmti.ac.ma</div>
                                            <div class="prefix-desc">Espace Scolarité</div>
                                        </div>
                                    </div>
                                    <div class="prefix-item">
                                        <div class="prefix-badge compta">compta</div>
                                        <div class="prefix-content">
                                            <div class="prefix-example">compta.nom.prenom@supmti.ac.ma</div>
                                            <div class="prefix-desc">Espace Comptabilité</div>
                                        </div>
                                    </div>
                                    <div class="prefix-item">
                                        <div class="prefix-badge rh">rh</div>
                                        <div class="prefix-content">
                                            <div class="prefix-example">rh.nom.prenom@supmti.ac.ma</div>
                                            <div class="prefix-desc">Espace Ressources Humaines</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-footer">
                                    <i class="fas fa-lightbulb"></i>
                                    <span>Les utilisateurs utiliseront ces emails pour se connecter après attribution</span>
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section Sécurité -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-lock"></i>
                        Sécurité
                    </h3>
                    <p class="section-subtitle">Définissez les informations de connexion</p>
                </div>
                
                <div class="form-grid">
                    <div class="form-field">
                        <label class="field-label">
                            <i class="fas fa-key"></i>
                            Mot de passe *
                        </label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" class="field-input" placeholder="Minimum 6 caractères" required>
                            <button type="button" class="password-toggle" onclick="togglePasswordField('password')">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label class="field-label">
                            <i class="fas fa-check-circle"></i>
                            Confirmer le mot de passe *
                        </label>
                        <div class="password-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="field-input" placeholder="Retapez le mot de passe" required>
                            <button type="button" class="password-toggle" onclick="togglePasswordField('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section Paramètres -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-cogs"></i>
                        Paramètres du compte
                    </h3>
                    <p class="section-subtitle">Configurez les options du compte utilisateur</p>
                </div>
                
                <div class="form-field">
                    <div class="toggle-field">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="toggle-label">
                            <span class="toggle-switch"></span>
                            <div class="toggle-content">
                                <span class="toggle-title">Compte actif</span>
                                <span class="toggle-subtitle">L'utilisateur peut se connecter et accéder au système</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section Attribution aux espaces -->
            <div class="form-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-building"></i>
                        Attribution aux espaces
                    </h3>
                    <p class="section-subtitle">Sélectionnez l'espace principal qui sera attribué à l'utilisateur (optionnel)</p>
                </div>
                
                <div class="espaces-grid">
                    @forelse($espaces as $espace)
                        <div class="espace-card">
                            <input type="radio" id="espace_{{ $espace->id }}" name="espace_id" value="{{ $espace->id }}" {{ !$espace->is_active ? 'disabled' : '' }}>
                            <label for="espace_{{ $espace->id }}" class="espace-card-label {{ !$espace->is_active ? 'disabled' : '' }}">
                                <div class="espace-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="espace-details">
                                    <h4>{{ $espace->nom }}</h4>
                                    <p>{{ $espace->space_type ?? 'Espace de travail' }}</p>
                                    <span class="espace-badge">{{ $espace->display_email ?? $espace->nom }}</span>
                                    @if(!$espace->is_active)
                                        <div class="espace-status inactive">
                                            <i class="fas fa-pause-circle"></i>
                                            Inactif
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @empty
                        <div class="no-espaces-modern">
                            <i class="fas fa-building"></i>
                            <h4>Aucun espace disponible</h4>
                            <p>Créez d'abord des espaces avant d'attribuer des utilisateurs</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="info-notice" style="margin-top: 1.5rem;">
                    <div class="notice-content">
                        <i class="fas fa-info-circle"></i>
                        <div class="notice-text">
                            <h4>Attribution optionnelle</h4>
                            <p>Vous pouvez créer l'utilisateur sans attribution et gérer ses accès plus tard via la "Gestion des Tâches".</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Layout moderne */
.modern-content {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 50%, var(--info-600) 100%);
    padding: 0;
    position: relative;
}

.modern-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(20, 184, 166, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.page-header {
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--secondary-600) 50%, var(--info-600) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 0;
    position: relative;
    z-index: 1;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

.breadcrumb-link {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: opacity 0.2s;
}

.breadcrumb-link:hover {
    opacity: 0.8;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
    max-width: 600px;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

/* Form layout */
.form-layout {
    max-width: 800px;
    margin: -2rem auto 0;
    padding: 0 2rem 2rem;
    position: relative;
    z-index: 1;
}

.modern-form {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    padding: 2rem;
    margin-top: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Sections du formulaire */
.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #f3f4f6;
}

.form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: #667eea;
    font-size: 1.1rem;
}

.section-subtitle {
    color: #6b7280;
    margin: 0;
    font-size: 0.95rem;
}

/* Photo upload moderne */
.photo-upload-modern {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    border: 2px dashed var(--gray-300);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.photo-upload-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(99, 102, 241, 0.02) 0%, 
        rgba(20, 184, 166, 0.02) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-upload-modern:hover {
    border-color: var(--primary-400);
    background: var(--primary-50);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.1);
}

.photo-upload-modern:hover::before {
    opacity: 1;
}

.photo-preview-modern {
    flex-shrink: 0;
}

.preview-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gray-200), var(--gray-300));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--gray-500);
    transition: all 0.3s ease;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.preview-placeholder:hover {
    background: linear-gradient(135deg, var(--primary-100), var(--primary-200));
    color: var(--primary-600);
    transform: scale(1.05);
}

.preview-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.25rem;
}

.preview-placeholder span {
    font-size: 0.75rem;
    text-align: center;
}

.preview-placeholder img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border: 3px solid white;
}

.photo-preview-modern img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.25);
    border: 3px solid white;
    transition: all 0.3s ease;
}

.photo-preview-modern img:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(99, 102, 241, 0.35);
}

.preview-image {
    width: 80px !important;
    height: 80px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.25) !important;
    border: 3px solid white !important;
    transition: all 0.3s ease !important;
    display: block !important;
}

.preview-image:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 12px 35px rgba(99, 102, 241, 0.35) !important;
}

.photo-controls {
    flex: 1;
    display: flex;
    gap: 0.75rem;
}

.btn-upload, .btn-remove {
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-lg);
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    position: relative;
    overflow: hidden;
    z-index: 2;
}

.btn-upload::before, .btn-remove::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-upload:hover::before, .btn-remove:hover::before {
    left: 100%;
}

.btn-upload {
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.btn-upload:hover {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
}

.btn-remove {
    background: linear-gradient(135deg, var(--danger-500), var(--danger-600));
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.btn-remove:hover {
    background: linear-gradient(135deg, var(--danger-600), var(--danger-700));
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.35);
}

.photo-info {
    color: #6b7280;
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

/* Grille de formulaire */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-field {
    margin-bottom: 0;
}

.field-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.field-label i {
    color: #667eea;
    width: 16px;
    text-align: center;
}

.field-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s;
    background: white;
}

.field-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.field-help {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.field-help i {
    color: var(--primary-500);
}

.email-info-toggle {
    background: none;
    border: none;
    color: var(--primary-600);
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.email-info-toggle:hover {
    background: var(--primary-50);
    color: var(--primary-700);
    transform: translateY(-1px);
}

.email-info-toggle i {
    font-size: 0.75rem;
}

/* Styles pour l'information des préfixes d'email */
.email-prefix-info {
    margin-top: 1rem;
    padding: 0;
    background: none;
    border: none;
    border-radius: 0.75rem;
    overflow: hidden;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.prefix-examples {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.prefix-examples::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-500), var(--secondary-500), var(--success-500));
}

.info-header {
    margin-bottom: 1.25rem;
    text-align: center;
}

.info-header h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.info-header h4 i {
    color: var(--primary-600);
    font-size: 0.9rem;
}

.info-header p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.5;
}

.prefix-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.prefix-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.prefix-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-300);
}

.prefix-badge {
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.8rem;
    font-weight: 700;
    font-family: monospace;
    min-width: 60px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.prefix-badge.scol {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
    border: 1px solid #86efac;
}

.prefix-badge.compta {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid #facc15;
}

.prefix-badge.rh {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
    border: 1px solid #60a5fa;
}

.prefix-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.prefix-example {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #e2e8f0;
}

.prefix-desc {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
    font-style: italic;
}

.info-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    background: linear-gradient(135deg, #fef7cd, #fef3c7);
    border: 1px solid #fbbf24;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    color: #92400e;
    font-weight: 500;
}

.info-footer i {
    color: #f59e0b;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .prefix-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
        text-align: left;
    }
    
    .prefix-badge {
        align-self: flex-start;
    }
    
    .prefix-content {
        width: 100%;
    }
    
    .prefix-examples {
        padding: 1rem;
    }
    
    .info-header h4 {
        font-size: 0.9rem;
    }
}

/* Styles pour l'email auto-généré */
.email-auto-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: linear-gradient(135deg, var(--success-100), var(--success-200));
    color: var(--success-700);
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
    animation: fadeIn 0.3s ease;
}

.email-input-wrapper {
    position: relative;
}

.email-domain-preview {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary-100);
    color: var(--primary-700);
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    font-family: monospace;
}

.email-domain-preview.show {
    opacity: 1;
}

.field-input.email-generated {
    background: linear-gradient(135deg, var(--success-50), var(--success-100)) !important;
    border-color: var(--success-400) !important;
}

.field-input.email-generated:focus {
    border-color: var(--success-500) !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1) !important;
}

.field-error {
    margin-top: 0.5rem;
    color: #dc2626;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.field-error::before {
    content: '\f071';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

/* Champ de mot de passe */
.password-field {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    transition: color 0.2s;
}

.password-toggle:hover {
    color: var(--primary-500);
}

/* Toggle switch moderne */
.toggle-field {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    width: 100%;
}

.toggle-switch {
    width: 48px;
    height: 24px;
    background: #d1d5db;
    border-radius: 12px;
    position: relative;
    transition: all 0.3s;
    flex-shrink: 0;
}

.toggle-switch::after {
    content: '';
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type="checkbox"]:checked + .toggle-label .toggle-switch {
    background: var(--primary-500);
}

input[type="checkbox"]:checked + .toggle-label .toggle-switch::after {
    transform: translateX(24px);
}

.toggle-content {
    flex: 1;
}

.toggle-title {
    font-weight: 500;
    color: #1f2937;
    display: block;
    margin-bottom: 0.25rem;
}

.toggle-subtitle {
    font-size: 0.85rem;
    color: #6b7280;
}

input[type="checkbox"] {
    display: none;
}

/* Grille des espaces */
.espaces-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.espace-card {
    position: relative;
}

.espace-card input[type="radio"] {
    display: none;
}

.espace-card-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
    height: 100%;
}

.espace-card-label:hover {
    border-color: var(--primary-500);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
}

.espace-card input[type="radio"]:checked + .espace-card-label {
    border-color: var(--primary-500);
    background: var(--primary-50);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
}

.espace-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s;
}

.espace-card input[type="radio"]:checked + .espace-card-label .espace-icon {
    background: var(--primary-500);
    color: white;
}

.espace-icon i {
    font-size: 1.25rem;
    color: #6b7280;
    transition: color 0.3s;
}

.espace-card input[type="radio"]:checked + .espace-card-label .espace-icon i {
    color: white;
}

.espace-details {
    flex: 1;
}

.espace-details h4 {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
}

.espace-details p {
    margin: 0 0 0.75rem 0;
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.4;
}

.espace-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 0.8rem;
    border-radius: 1rem;
    font-family: monospace;
    font-weight: 500;
}

.espace-card input[type="radio"]:checked + .espace-card-label .espace-badge {
    background: var(--primary-100);
    color: var(--primary-700);
}

.espace-card-label.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f9fafb;
}

.espace-card-label.disabled:hover {
    transform: none;
    box-shadow: none;
    border-color: #e5e7eb;
}

.espace-status.inactive {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #dc2626;
    margin-top: 0.5rem;
    font-weight: 600;
}

.espace-status.inactive i {
    font-size: 0.75rem;
}

.no-espaces-modern {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem 2rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    border: 2px dashed #d1d5db;
}

.no-espaces-modern i {
    font-size: 2.5rem;
    color: var(--primary-500);
    margin-bottom: 1rem;
}

.no-espaces-modern h4 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.no-espaces-modern p {
    margin: 0;
    color: #6b7280;
}

/* Section d'information */
.info-notice {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 1px solid #0284c7;
    border-radius: 0.75rem;
    padding: 1.5rem;
}

.notice-content {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.notice-content i {
    color: #0284c7;
    font-size: 1.5rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.notice-text h4 {
    margin: 0 0 0.5rem 0;
    color: #0f172a;
    font-size: 1rem;
    font-weight: 600;
}

.notice-text p {
    margin: 0;
    color: #334155;
    line-height: 1.5;
}

/* Actions du formulaire */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #f3f4f6;
}

.btn-cancel, .btn-primary {
    padding: 0.875rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    border: none;
}

.btn-cancel {
    background: #f3f4f6;
    color: #6b7280;
}

.btn-cancel:hover {
    background: #e5e7eb;
    color: #374151;
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.25);
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .form-layout {
        padding: 0 1rem 2rem;
    }
    
    .modern-form {
        padding: 1.5rem;
    }
    
    .photo-upload-modern {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
        padding: 1.25rem;
    }
    
    .photo-controls {
        flex-direction: column;
        width: 100%;
        gap: 0.5rem;
    }
    
    .btn-upload, .btn-remove {
        width: 100%;
        justify-content: center;
        padding: 0.875rem 1rem;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .btn-cancel, .btn-primary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Pas de données d'espaces nécessaires - attribution manuelle via gestion des tâches

function togglePasswordField(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const passwordIcon = document.getElementById(fieldId + '-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Email manual input - pas de génération automatique
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    
    // Fonction pour suggérer un email basé sur le nom (optionnel)
    function suggestEmail() {
        const userName = nameInput?.value?.trim();
        if (userName && emailInput && !emailInput.value) {
            const cleanName = userName.toLowerCase()
                .replace(/[àáâãäå]/g, 'a')
                .replace(/[èéêë]/g, 'e')
                .replace(/[ìíîï]/g, 'i')
                .replace(/[òóôõö]/g, 'o')
                .replace(/[ùúûü]/g, 'u')
                .replace(/[ýÿ]/g, 'y')
                .replace(/[ñ]/g, 'n')
                .replace(/[ç]/g, 'c')
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '.')
                .trim();
            
            emailInput.placeholder = cleanName + '@supmti.ac.ma';
        }
    }
    
    nameInput?.addEventListener('input', function() {
        clearTimeout(this.emailTimeout);
        this.emailTimeout = setTimeout(suggestEmail, 300);
    });
});

document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    
    if (file) {
        // Validation côté client
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!validTypes.includes(file.type)) {
            alert('Format non supporté. Utilisez JPG, PNG ou GIF.');
            e.target.value = '';
            return;
        }
        
        if (file.size > maxSize) {
            alert('Fichier trop volumineux. Taille maximum: 2MB.');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu photo" class="preview-image">`;
            removeBtn.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
});

function removePhoto() {
    const input = document.getElementById('profile_photo');
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    
    input.value = '';
    preview.innerHTML = `
        <div class="preview-placeholder">
            <i class="fas fa-user-circle"></i>
            <span>Aucune photo</span>
        </div>
    `;
    removeBtn.style.display = 'none';
}

function toggleEmailInfo() {
    const emailInfo = document.getElementById('emailPrefixInfo');
    const toggleIcon = document.getElementById('emailToggleIcon');
    const toggleText = document.getElementById('emailToggleText');
    
    if (emailInfo.style.display === 'none') {
        emailInfo.style.display = 'block';
        toggleIcon.classList.remove('fa-question-circle');
        toggleIcon.classList.add('fa-eye-slash');
        toggleText.textContent = 'Masquer les exemples';
    } else {
        emailInfo.style.display = 'none';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-question-circle');
        toggleText.textContent = 'Voir les exemples';
    }
}
</script>
@endsection