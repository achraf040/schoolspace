@extends('admin.layout')

@section('title', 'Modifier un Utilisateur')
@section('page-title', 'Modifier un Utilisateur')

@section('content')
<div class="content-section">
    <x-admin.page-header 
        title="Modifier : {{ $user->name }}" 
        :back-route="route('admin.users.index')" />

    <div class="form-container">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Photo de profil -->
            <div class="form-group">
                <label class="form-label">Photo de profil</label>
                <div class="photo-upload-container">
                    <div class="photo-preview" id="photoPreview">
                        @if($user->hasProfilePhoto())
                            <img src="{{ $user->profile_photo_url }}" alt="Photo de {{ $user->name }}">
                        @else
                            <i class="fas fa-user-circle"></i>
                            <p>Aucune photo</p>
                        @endif
                    </div>
                    <div class="photo-upload-actions">
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('profile_photo').click()">
                            <i class="fas fa-camera"></i>
                            {{ $user->hasProfilePhoto() ? 'Changer la photo' : 'Ajouter une photo' }}
                        </button>
                        @if($user->hasProfilePhoto())
                            <button type="button" class="btn btn-outline btn-danger" onclick="removeCurrentPhoto()">
                                <i class="fas fa-trash"></i>
                                Supprimer la photo
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline btn-danger" id="removePhotoBtn" onclick="removePhoto()" style="display: none;">
                            <i class="fas fa-trash"></i>
                            Annuler
                        </button>
                    </div>
                    <div class="photo-help">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Formats acceptés: JPG, PNG, GIF. Taille max: 2MB. Dimensions: 100x100 à 2000x2000 pixels.
                        </small>
                    </div>
                </div>
                <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                @error('profile_photo')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            
            <x-admin.form-input 
                name="name" 
                label="Nom complet" 
                placeholder="Ex: Ahmed Ben Ali"
                :value="$user->name"
                required />

            <!-- Email généré automatiquement (non modifiable) -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope"></i>
                    Adresse email
                    <span class="auto-generated-badge">
                        <i class="fas fa-magic"></i>
                        Auto-généré
                    </span>
                </label>
                <div class="email-display-field">
                    <input type="text" class="form-input email-readonly" value="{{ $user->display_email }}" readonly>
                    <div class="email-info">
                        <i class="fas fa-info-circle"></i>
                        Email généré automatiquement selon les espaces attribués
                    </div>
                </div>
                <!-- Hidden field avec l'email original pour validation -->
                <input type="hidden" name="email" value="{{ $user->email }}">
            </div>

            <x-admin.form-password 
                name="password" 
                label="Nouveau mot de passe" 
                placeholder="Laisser vide pour conserver l'actuel" />

            <x-admin.form-password 
                name="password_confirmation" 
                label="Confirmer le nouveau mot de passe" 
                placeholder="Confirmer le nouveau mot de passe" />

            <x-admin.form-checkbox 
                name="is_active" 
                label="Compte actif"
                help="Si décoché, l'utilisateur ne pourra pas se connecter"
                :checked="old('is_active', $user->is_active)" />

            <!-- Information sur les tâches -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-tasks"></i>
                    Tâches d'attribution d'espaces
                </label>
                <div class="info-box">
                    @if($user->espaces->count() > 0)
                        <div class="current-attributions">
                            <h4>Espaces actuellement attribués :</h4>
                            <ul>
                                @foreach($user->espaces as $espace)
                                    <li>
                                        <strong>{{ $espace->nom }}</strong>
                                        <span class="espace-email">{{ $espace->display_email }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="no-attributions">
                            <i class="fas fa-info-circle"></i>
                            <p>Aucune tâche d'attribution d'espace configurée pour cet utilisateur.</p>
                        </div>
                    @endif
                    <div class="form-help">
                        <i class="fas fa-arrow-right"></i>
                        Pour gérer les attributions d'espaces, utilisez la <a href="{{ route('admin.attributions.index') }}" class="link">page des tâches</a>
                    </div>
                </div>
            </div>

            <x-admin.form-actions 
                :cancel-route="route('admin.users.index')"
                submit-text="Modifier l'utilisateur" />
        </form>
    </div>
</div>

<style>
/* Email auto-généré */
.auto-generated-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.email-display-field {
    position: relative;
}

.email-readonly {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe) !important;
    border: 2px solid #0284c7 !important;
    color: #0c4a6e !important;
    font-weight: 500;
    cursor: not-allowed;
}

.email-readonly:focus {
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1) !important;
}

.email-info {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    font-size: 12px;
    color: #64748b;
    font-style: italic;
}

.email-info i {
    color: #0284c7;
}

.photo-upload-container {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    background: #f9fafb;
    transition: all 0.3s ease;
}

.photo-upload-container:hover {
    border-color: #0f766e;
    background: #f0fdfa;
}

.photo-preview {
    margin-bottom: 16px;
}

.photo-preview i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 8px;
}

.photo-preview img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #0f766e;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
}

.photo-preview p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
}

.photo-upload-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.photo-help {
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.photo-help small {
    color: #6b7280;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
}

.photo-help i {
    color: #0f766e;
}

.form-error {
    color: #dc2626;
    font-size: 12px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-error::before {
    content: '\f071';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

/* Information sur les tâches */
.info-box {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    background: #f8fafc;
    margin-top: 12px;
}

.current-attributions h4 {
    margin: 0 0 16px 0;
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
}

.current-attributions ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.current-attributions li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
}

.current-attributions li:last-child {
    margin-bottom: 0;
}

.espace-email {
    font-family: 'Courier New', Monaco, monospace;
    font-size: 12px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 6px;
}

.no-attributions {
    text-align: center;
    padding: 24px;
    color: #6b7280;
}

.no-attributions i {
    font-size: 24px;
    margin-bottom: 8px;
    color: #d1d5db;
}

.no-attributions p {
    margin: 0;
    font-size: 14px;
}

.link {
    color: #0f766e;
    text-decoration: none;
    font-weight: 500;
}

.link:hover {
    color: #14b8a6;
    text-decoration: underline;
}

.form-help {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #6b7280;
    margin-top: 12px;
}

.form-help i {
    color: #0f766e;
    font-size: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .espaces-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .espace-card-label {
        padding: 12px;
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .espace-details h4 {
        white-space: normal;
        text-align: center;
    }
    
    .espace-details p {
        white-space: normal;
        text-align: center;
    }
}
</style>

<script>
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

document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    const removePhotoInput = document.getElementById('remove_photo');
    
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
            preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu photo">`;
            removeBtn.style.display = 'inline-flex';
            removePhotoInput.value = '0'; // Ne pas supprimer l'ancienne photo si on en upload une nouvelle
        };
        reader.readAsDataURL(file);
    }
});

function removePhoto() {
    const input = document.getElementById('profile_photo');
    const preview = document.getElementById('photoPreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    const removePhotoInput = document.getElementById('remove_photo');
    
    input.value = '';
    @if($user->hasProfilePhoto())
        preview.innerHTML = `<img src="{{ $user->profile_photo_url }}" alt="Photo de {{ $user->name }}">`;
    @else
        preview.innerHTML = `
            <i class="fas fa-user-circle"></i>
            <p>Aucune photo</p>
        `;
    @endif
    removeBtn.style.display = 'none';
    removePhotoInput.value = '0';
}

function removeCurrentPhoto() {
    if (confirm('Êtes-vous sûr de vouloir supprimer la photo de profil actuelle ?')) {
        const preview = document.getElementById('photoPreview');
        const removePhotoInput = document.getElementById('remove_photo');
        
        preview.innerHTML = `
            <i class="fas fa-user-circle"></i>
            <p>Photo supprimée</p>
        `;
        removePhotoInput.value = '1';
    }
}
</script>
@endsection