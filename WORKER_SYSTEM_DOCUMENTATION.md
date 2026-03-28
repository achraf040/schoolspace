# 📋 Documentation - Système de Gestion des Travailleurs

## 🎯 Aperçu du Système

Le système de gestion des travailleurs permet aux employés de l'école de se connecter à leur espace de travail personnel et de gérer leurs tâches assignées. Le système utilise l'email pour déterminer automatiquement à quel espace appartient chaque travailleur.

## 🔐 Authentification des Travailleurs

### Format d'Email Requis
Les travailleurs doivent avoir un email au format :
```
nomprenom@nomEspace-supmti.ma
```

**Exemples :**
- `ahmedbenali@informatique-supmti.ma` → Espace "Informatique"
- `fatimazahra@gestion-supmti.ma` → Espace "Gestion"
- `omarmansouri@comptabilite-supmti.ma` → Espace "Comptabilité"

### Processus d'Authentification
1. Le travailleur saisit son email et mot de passe
2. Le système détecte automatiquement son espace via l'email
3. Si aucun espace n'est trouvé, une attribution automatique est tentée
4. Redirection vers le dashboard du travailleur

## 🏢 Gestion des Espaces

### Détection Automatique
- Le système analyse la partie avant le domaine principal dans l'email
- Il recherche l'espace correspondant dans la base de données
- Attribution automatique si un espace compatible est trouvé

### Sécurité
- Chaque travailleur ne peut accéder qu'à SON propre espace
- Isolation complète des données entre espaces
- Vérifications multiples pour empêcher l'accès non autorisé

## 📝 Gestion des Tâches

### Types de Statut
- **En attente** (`pending`) : Tâche assignée mais pas encore commencée
- **En cours** (`active`) : Tâche actuellement en cours de réalisation
- **Terminée** (`completed`) : Tâche complètement achevée

### Fonctionnalités Disponibles
1. **Visualisation des tâches** : Liste de toutes les tâches assignées
2. **Filtrage** : Par statut (toutes, actives, en attente, terminées)
3. **Mise à jour du statut** : Changement du statut en temps réel
4. **Détails complets** : Vue détaillée de chaque tâche
5. **Historique** : Suivi des modifications et dates importantes

## 🖥️ Interface Utilisateur

### Dashboard Principal
- **Statistiques** : Nombre total, actives, en attente, terminées
- **Informations de l'espace** : Détails sur l'espace assigné
- **Liste des tâches** : Avec filtres et actions
- **Heure en temps réel** : Affichage de la date/heure courante

### Page de Détails
- **Description complète** de la tâche
- **Informations temporelles** : Dates de création, début, fin
- **Gestion du statut** : Modification interactive
- **Historique** : Timeline des changements

## 🔧 Installation et Configuration

### 1. Migrations de Base de Données
```bash
php artisan migrate
```

### 2. Données de Test (Optionnel)
```bash
php artisan db:seed --class=WorkerTestSeeder
```

### 3. Comptes de Test Créés
- **Email**: `ahmedbenali@informatique-supmti.ma`
- **Email**: `fatimazahra@gestion-supmti.ma`  
- **Email**: `omarmansouri@comptabilite-supmti.ma`
- **Mot de passe**: `password123` (pour tous)

## 🛡️ Sécurité Implémentée

### Contrôles d'Accès
1. **Middleware WorkerOnly** : Seuls les non-admins actifs peuvent accéder
2. **Vérification d'espace** : Chaque travailleur ne voit que ses données
3. **Validation des actions** : Toute action sur une tâche est vérifiée
4. **Logs de sécurité** : Enregistrement des accès pour audit

### Isolation des Données
- Les requêtes filtrent automatiquement par `user_id` et `espace_id`
- Impossibilité d'accéder aux données d'autres espaces
- Déconnexion automatique en cas de tentative non autorisée

## 📊 Base de Données

### Nouvelles Tables/Colonnes Ajoutées
- `task_status` dans `attributions` : Statut spécifique aux tâches
- `title` dans `attributions` : Titre optionnel de la tâche
- `completed_at` dans `attributions` : Date de completion

### Relations
- `User` → `Attribution` (One-to-Many)
- `Espace` → `Attribution` (One-to-Many)
- `User` → `Espace` (Many-to-Many via `attributions`)

## 🌐 Routes Disponibles

### Routes Travailleurs
- `GET /worker/dashboard` : Dashboard principal
- `GET /worker/task/{id}` : Détails d'une tâche
- `PATCH /worker/task/{id}/status` : Mise à jour du statut

### Authentification
- `GET /` : Page de connexion
- `POST /` : Traitement de la connexion
- `POST /logout` : Déconnexion

## 🎨 Design et Thème

### Couleurs Principales
- **Vert** (`#059669`) : Couleur principale pour les travailleurs
- **Contraste** avec le bleu des admins pour différenciation claire
- **Interface cohérente** avec la charte graphique SUPMTI

### Responsive Design
- Interface adaptée mobile/tablet/desktop
- Navigation tactile optimisée
- Cartes de tâches redimensionnables

## 🚀 Utilisation Quotidienne

### Pour les Travailleurs
1. Se connecter avec email professionnel
2. Consulter les tâches assignées sur le dashboard
3. Mettre à jour le statut au fur et à mesure
4. Consulter les détails si nécessaire
5. Se déconnecter à la fin

### Pour les Administrateurs
1. Créer des espaces via l'interface admin
2. Créer des travailleurs avec emails appropriés
3. Assigner des tâches (attributions) via l'interface admin
4. Suivre l'avancement via les outils admin

## ⚡ Performance et Optimisation

### Fonctionnalités Optimisées
- Requêtes avec `eager loading` pour éviter N+1
- Cache des relations utilisateur-espace
- Filtrage côté base de données
- Interface AJAX pour les mises à jour de statut

### Logging et Monitoring
- Logs d'accès des travailleurs
- Suivi des modifications de statut
- Détection des tentatives d'accès non autorisé

---

## 📞 Support

Pour toute question ou problème :
1. Vérifier que l'email respecte le format requis
2. S'assurer que l'espace correspondant existe
3. Contacter l'administrateur pour attribution manuelle si nécessaire

---

**✨ Le système est maintenant prêt à être utilisé par les travailleurs de l'école !**