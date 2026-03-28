# SchoolSpace

Application web de gestion des espaces et des tâches pour établissements scolaires, développée avec **Laravel 10** et **PHP 8.1+**.

---

## Présentation

SchoolSpace permet aux administrateurs de gérer les utilisateurs, les espaces (départements/bureaux) et les attributions de tâches. Les agents (workers) disposent d'un tableau de bord personnel pour suivre et mettre à jour leurs tâches assignées.

---

## Fonctionnalités

### Espace Admin
- Gestion complète des utilisateurs (CRUD, activation/désactivation, photo de profil)
- Gestion des espaces (départements) avec génération automatique d'email
- Attribution de tâches aux agents par espace
- Types de tâches : accès, réservation, permanent, temporaire, maintenance, administration
- Suivi des statuts : en attente, actif, en pause, terminé
- Tableau de bord avec statistiques et activité récente

### Espace Agent
- Vue des tâches assignées avec filtrage par espace
- Mise à jour du statut des tâches en temps réel
- Historique des tâches complétées
- Statistiques personnelles

### Sécurité
- Authentification par session avec protection CSRF
- Limitation des tentatives de connexion (rate limiting)
- Contrôle d'accès basé sur les rôles (admin / agent)
- Génération de tokens sécurisés

---

## Stack technique

| Technologie | Version |
|---|---|
| PHP | ^8.1 |
| Laravel | ^10.10 |
| Laravel Sanctum | ^3.3 |
| PostgreSQL | 13+ |
| Vite | ^5.0 |
| Axios | ^1.6 |

---

## Installation

### Prérequis
- PHP >= 8.1
- Composer
- Node.js & npm
- PostgreSQL

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/achraf040/schoolspace.git
cd schoolspace

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_CONNECTION=pgsql
# DB_DATABASE=schoolspace
# DB_USERNAME=postgres
# DB_PASSWORD=

# 6. Exécuter les migrations et les seeders
php artisan migrate --seed

# 7. Créer le lien de stockage
php artisan storage:link

# 8. Compiler les assets
npm run build

# 9. Lancer le serveur
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`.

---

## Structure de la base de données

| Table | Description |
|---|---|
| `users` | Utilisateurs (admin / agents) |
| `espaces` | Espaces / départements |
| `attributions` | Tâches assignées aux agents par espace |

---

## Comptes par défaut (Seeder)

Après `php artisan migrate --seed`, un compte administrateur est créé automatiquement. Consultez `database/seeders/AdminSeeder.php` pour les identifiants.

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   ├── WorkerController.php
│   │   └── Admin/
│   │       ├── UserController.php
│   │       ├── EspaceController.php
│   │       └── AttributionController.php
│   └── Middleware/
│       ├── AdminOnly.php
│       ├── WorkerOnly.php
│       └── LoginRateLimit.php
├── Models/
│   ├── User.php
│   ├── Espace.php
│   └── Attribution.php
└── Services/
    ├── EmailGenerationService.php
    └── CacheService.php
```

---

## Licence

Ce projet est développé dans un cadre éducatif.
