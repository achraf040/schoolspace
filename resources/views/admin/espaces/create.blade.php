@extends('admin.layout')

@section('title', 'Créer un Espace')
@section('page-title', 'Nouvel Espace')

@section('content')
<div class="espace-form-container">
    <div class="form-header">
        <div class="form-title">
            <i class="fas fa-building"></i>
            <h2>Créer un nouvel espace</h2>
        </div>
        <a href="{{ route('admin.espaces.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <form action="{{ route('admin.espaces.store') }}" method="POST" class="espace-form">
        @csrf
        
        <div class="form-grid">
            <!-- Informations principales -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Informations de base
                    </h3>
                    <p>Définissez les informations principales de l'espace</p>
                </div>

                <div class="form-group">
                    <label class="form-label required">Nom de l'espace</label>
                    <input type="text" 
                           name="nom" 
                           id="nom"
                           class="form-input" 
                           value="{{ old('nom') }}"
                           placeholder="Ex: Salle Informatique, Bibliothèque..."
                           required>
                    @error('nom')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-textarea" 
                              rows="4"
                              placeholder="Description détaillée de l'espace, son utilisation, ses équipements...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="checkbox-container">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active"
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="checkbox-label">
                            <i class="fas fa-check"></i>
                            Espace actif
                        </label>
                    </div>
                    <div class="form-help">
                        Si décoché, l'espace ne pourra pas être attribué aux utilisateurs
                    </div>
                </div>
            </div>

            <!-- Configuration email -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-envelope"></i>
                        Adresse email
                    </h3>
                    <p>Configurez l'adresse email associée à cet espace</p>
                </div>

                <div class="email-config">
                    <div class="form-group">
                        <label class="form-label required">Email de l'espace</label>
                        <div class="email-input-group">
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   class="form-input" 
                                   value="{{ old('email') }}"
                                   placeholder="espace@exemple.com"
                                   required>
                            <button type="button" class="btn btn-secondary" id="generateEmailBtn">
                                <i class="fas fa-magic"></i>
                                Auto-générer
                            </button>
                        </div>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        <div class="form-help">
                            <i class="fas fa-lightbulb"></i>
                            L'email peut être auto-généré basé sur le nom de l'espace
                        </div>
                    </div>

                    <div class="email-preview" id="emailPreview" style="display: none;">
                        <div class="preview-header">
                            <i class="fas fa-eye"></i>
                            Aperçu de l'email
                        </div>
                        <div class="preview-content">
                            <span id="previewEmail"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i>
                Créer l'espace
            </button>
            <a href="{{ route('admin.espaces.index') }}" class="btn btn-outline btn-lg">
                <i class="fas fa-times"></i>
                Annuler
            </a>
        </div>
    </form>
</div>

<style>
.espace-form-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 20px;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.form-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-title i {
    font-size: 24px;
    color: #0f766e;
}

.form-title h2 {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.espace-form {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 32px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.section-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.section-header i {
    color: #0f766e;
    font-size: 20px;
}

.section-header p {
    color: #6b7280;
    font-size: 14px;
    margin: 0;
    line-height: 1.5;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-label.required::after {
    content: '*';
    color: #dc2626;
    font-weight: bold;
}

.form-input,
.form-textarea {
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.email-input-group {
    display: flex;
    gap: 12px;
    align-items: stretch;
}

.email-input-group .form-input {
    flex: 1;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.checkbox-container:hover {
    background: #f3f4f6;
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

.checkbox-label i {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: transparent;
    transition: all 0.2s ease;
}

.checkbox-container input[type="checkbox"]:checked + .checkbox-label i {
    background: #0f766e;
    border-color: #0f766e;
    color: white;
}

.email-preview {
    background: #f0fdfa;
    border: 1px solid #5eead4;
    border-radius: 8px;
    padding: 16px;
    margin-top: 12px;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #0f766e;
    margin-bottom: 8px;
}

.preview-content {
    font-family: monospace;
    font-size: 14px;
    color: #134e4a;
    background: white;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #a7f3d0;
}

.form-help {
    font-size: 12px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-help i {
    color: #0f766e;
}

.form-error {
    color: #dc2626;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-error::before {
    content: '\f071';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.form-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-lg {
    padding: 16px 32px;
    font-size: 16px;
}

.btn-primary {
    background: #0f766e;
    color: white;
}

.btn-primary:hover {
    background: #134e4a;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-outline {
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .form-header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .email-input-group {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nomInput = document.getElementById('nom');
    const emailInput = document.getElementById('email');
    const generateBtn = document.getElementById('generateEmailBtn');
    const emailPreview = document.getElementById('emailPreview');
    const previewEmail = document.getElementById('previewEmail');

    // Auto-génération d'email
    generateBtn.addEventListener('click', function() {
        const nom = nomInput.value.trim();
        if (!nom) {
            alert('Veuillez d\'abord saisir le nom de l\'espace');
            nomInput.focus();
            return;
        }

        fetch('{{ route("admin.espaces.generate-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nom: nom })
        })
        .then(response => response.json())
        .then(data => {
            if (data.email) {
                emailInput.value = data.email;
                previewEmail.textContent = data.email;
                emailPreview.style.display = 'block';
                
                // Animation de succès
                generateBtn.style.background = '#10b981';
                generateBtn.innerHTML = '<i class="fas fa-check"></i> Généré !';
                
                setTimeout(() => {
                    generateBtn.style.background = '';
                    generateBtn.innerHTML = '<i class="fas fa-magic"></i> Auto-générer';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la génération de l\'email');
        });
    });

    // Prévisualisation en temps réel
    emailInput.addEventListener('input', function() {
        if (this.value.trim()) {
            previewEmail.textContent = this.value;
            emailPreview.style.display = 'block';
        } else {
            emailPreview.style.display = 'none';
        }
    });
});
</script>
@endsection