# Design System SUPMTI 2024

## 🎨 Palette de Couleurs

### Couleur Principale - Indigo
Moderne et professionnelle, utilisée pour les éléments principaux et la navigation.

```css
--primary-50: #eef2ff
--primary-100: #e0e7ff
--primary-200: #c7d2fe
--primary-300: #a5b4fc
--primary-400: #818cf8
--primary-500: #6366f1  /* Couleur principale */
--primary-600: #4f46e5
--primary-700: #4338ca
--primary-800: #3730a3
--primary-900: #312e81
--primary-950: #1e1b4b
```

### Couleur Secondaire - Teal/Cyan
Complémentaire, utilisée pour les accents et les éléments secondaires.

```css
--secondary-50: #f0fdfa
--secondary-100: #ccfbf1
--secondary-200: #99f6e4
--secondary-300: #5eead4
--secondary-400: #2dd4bf
--secondary-500: #14b8a6  /* Couleur secondaire */
--secondary-600: #0d9488
--secondary-700: #0f766e
--secondary-800: #115e59
--secondary-900: #134e4a
```

### Couleurs Neutres - Slate
Base moderne pour le texte et les backgrounds.

```css
--gray-50: #f8fafc
--gray-100: #f1f5f9
--gray-200: #e2e8f0
--gray-300: #cbd5e1
--gray-400: #94a3b8
--gray-500: #64748b
--gray-600: #475569
--gray-700: #334155
--gray-800: #1e293b  /* Texte principal */
--gray-900: #0f172a
--gray-950: #020617
```

### Couleurs Fonctionnelles

#### Succès - Vert
```css
--success-500: #22c55e
--success-100: #dcfce7  /* Background clair */
--success-700: #15803d  /* Texte foncé */
```

#### Attention - Orange
```css
--warning-500: #f59e0b
--warning-100: #fef3c7
--warning-700: #b45309
```

#### Danger - Rouge
```css
--danger-500: #ef4444
--danger-100: #fee2e2
--danger-700: #b91c1c
```

#### Information - Bleu
```css
--info-500: #3b82f6
--info-100: #dbeafe
--info-700: #1d4ed8
```

## 🧩 Composants

### Boutons Modernes
```html
<!-- Bouton principal -->
<button class="btn-modern btn-primary">Action principale</button>

<!-- Bouton secondaire -->
<button class="btn-modern btn-secondary">Action secondaire</button>

<!-- Variantes de taille -->
<button class="btn-modern btn-primary btn-lg">Grand bouton</button>
<button class="btn-modern btn-primary btn-sm">Petit bouton</button>
```

### Cartes Modernes
```html
<!-- Carte avec gradient -->
<div class="card-modern card-gradient card-primary">
    <!-- Contenu -->
</div>

<!-- Carte simple -->
<div class="card-modern">
    <!-- Contenu -->
</div>
```

### Badges
```html
<!-- Badge de statut -->
<span class="badge-modern badge-success">Actif</span>
<span class="badge-modern badge-warning">En attente</span>
<span class="badge-modern badge-danger">Inactif</span>
```

### Formulaires
```html
<div class="form-group-modern">
    <label class="form-label-modern required">
        <i class="fas fa-user"></i>
        Nom d'utilisateur
    </label>
    <input type="text" class="form-input-modern" placeholder="Entrez votre nom">
    <div class="form-help-modern">
        <i class="fas fa-info-circle"></i>
        Minimum 3 caractères
    </div>
</div>
```

### Alertes
```html
<!-- Alerte de succès -->
<div class="alert-modern alert-success">
    <i class="fas fa-check-circle alert-icon"></i>
    <div>Opération réussie avec succès!</div>
</div>

<!-- Alerte d'erreur -->
<div class="alert-modern alert-danger">
    <i class="fas fa-exclamation-triangle alert-icon"></i>
    <div>Une erreur s'est produite.</div>
</div>
```

## 🎭 Effets Visuels

### Effet de Verre (Glassmorphism)
```css
.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
```

### Texte en Dégradé
```css
.gradient-text {
    background: linear-gradient(135deg, var(--primary-600), var(--secondary-600));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
```

### Ombres Modernes
```css
.shadow-modern {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-modern-lg {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
```

## 🎬 Animations

### Classes d'Animation
```html
<!-- Animation d'entrée -->
<div class="animate-fade-in-up animate-delay-200">Contenu animé</div>
<div class="animate-scale-in animate-delay-300">Contenu qui grandit</div>
<div class="animate-slide-down">Contenu qui glisse</div>
```

### Délais Disponibles
- `animate-delay-100` : 0.1s
- `animate-delay-200` : 0.2s  
- `animate-delay-300` : 0.3s
- `animate-delay-400` : 0.4s
- `animate-delay-500` : 0.5s

## 📱 Responsive Design

Le système est conçu "mobile-first" avec des breakpoints modernes :

- **Mobile** : < 768px
- **Tablet** : 768px - 1024px  
- **Desktop** : > 1024px
- **Large Desktop** : > 1280px

## 🔧 Variables CSS Importantes

### Rayons de Bordure
```css
--radius-sm: 0.375rem    /* 6px */
--radius: 0.5rem         /* 8px */
--radius-md: 0.75rem     /* 12px */
--radius-lg: 1rem        /* 16px */
--radius-xl: 1.5rem      /* 24px */
```

### Ombres
```css
--shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05)
--shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)
--shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)
--shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)
--shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)
```

### Transitions
```css
--transition-fast: 150ms ease-in-out
--transition-normal: 200ms ease-in-out
--transition-slow: 300ms ease-in-out
```

## 🎯 Utilisation Recommandée

1. **Cohérence** : Utilisez toujours les variables CSS définies
2. **Hiérarchie** : Respectez l'ordre des couleurs (50-950)
3. **Accessibilité** : Vérifiez les contrastes (minimum 4.5:1)
4. **Performance** : Utilisez les classes utilitaires plutôt que du CSS custom
5. **Responsive** : Testez sur tous les breakpoints

## 📈 Évolution

Ce design system est vivant et peut évoluer selon les besoins du projet. Toute modification doit être :

1. **Documentée** dans ce fichier
2. **Testée** sur tous les composants
3. **Validée** par l'équipe
4. **Compatible** avec l'existant

---

*Design System SUPMTI v2024.1 - Créé pour une expérience utilisateur moderne et professionnelle*