# ✅ Liste de Vérification - Système Travailleurs

## 🔧 Tests de Base de Données

### 1. Migrations
- [ ] Vérifier que toutes les migrations s'exécutent sans erreur
- [ ] Vérifier que la table `attributions` a les nouvelles colonnes :
  - `task_status` (enum)
  - `title` (string nullable)
  - `completed_at` (timestamp nullable)

### 2. Relations
- [ ] User → Espaces (Many-to-Many via attributions)
- [ ] User → Attributions (One-to-Many)
- [ ] Espace → Attributions (One-to-Many)

## 🔐 Tests d'Authentification

### 1. Connexion Admin
- [ ] Admin peut se connecter et accéder à l'interface admin
- [ ] Admin ne peut PAS accéder aux routes worker

### 2. Connexion Travailleur
- [ ] Travailleur peut se connecter avec email valide
- [ ] Redirection automatique vers `/worker/dashboard`
- [ ] Détection automatique de l'espace basée sur l'email

### 3. Sécurité
- [ ] Utilisateur inactif ne peut pas se connecter
- [ ] Email sans espace correspondant affiche une erreur claire
- [ ] Déconnexion automatique pour accès non autorisé

## 🏢 Tests de Gestion des Espaces

### 1. Attribution Automatique
- [ ] Email `nom@informatique-supmti.ma` → Espace "Informatique"
- [ ] Email `nom@gestion-supmti.ma` → Espace "Gestion"
- [ ] Attribution créée automatiquement si espace trouvé

### 2. Isolation des Données
- [ ] Travailleur A ne voit que ses tâches
- [ ] Travailleur A ne peut pas modifier les tâches de B
- [ ] Tentative d'accès direct à tâche d'autrui → Erreur 403

## 📋 Tests de Gestion des Tâches

### 1. Affichage
- [ ] Dashboard affiche toutes les tâches du travailleur
- [ ] Statistiques correctes (total, actives, en attente, terminées)
- [ ] Filtres fonctionnent (tous, actives, en attente, terminées)

### 2. Mise à Jour de Statut
- [ ] Changement de "En attente" → "En cours" fonctionne
- [ ] Changement de "En cours" → "Terminée" fonctionne
- [ ] Date `completed_at` définie lors du passage à "Terminée"
- [ ] Notification de succès affichée

### 3. Détails des Tâches
- [ ] Page détails accessible via bouton "Détails"
- [ ] Toutes les informations affichées correctement
- [ ] Mise à jour de statut fonctionne depuis la page détails
- [ ] Historique/Timeline affiché

## 🖥️ Tests d'Interface

### 1. Responsive Design
- [ ] Interface s'adapte sur mobile
- [ ] Navigation sidebar fonctionne
- [ ] Cartes de tâches redimensionnables

### 2. UX/UI
- [ ] Couleurs cohérentes (vert pour workers vs bleu pour admins)
- [ ] Icônes appropriées pour chaque statut
- [ ] Messages d'erreur/succès clairs
- [ ] Heure en temps réel mise à jour

### 3. Navigation
- [ ] Menu sidebar avec liens corrects
- [ ] Bouton retour sur page détails
- [ ] Déconnexion fonctionne
- [ ] Logo/branding cohérent

## ⚡ Tests de Performance

### 1. Requêtes
- [ ] Pas de requêtes N+1 sur dashboard
- [ ] Relations chargées avec eager loading
- [ ] Filtres appliqués côté DB (pas en PHP)

### 2. Sécurité des Requêtes  
- [ ] Toutes les requêtes filtrent par `user_id`
- [ ] Vérification `espace_id` pour double sécurité
- [ ] Pas d'exposition de données sensibles

## 🧪 Données de Test

### 1. Seeders
- [ ] `WorkerTestSeeder` s'exécute sans erreur
- [ ] 3 travailleurs créés avec espaces différents
- [ ] Chaque travailleur a 3 tâches avec statuts différents

### 2. Comptes de Test
- [ ] `ahmedbenali@informatique-supmti.ma` → Espace Informatique
- [ ] `fatimazahra@gestion-supmti.ma` → Espace Gestion  
- [ ] `omarmansouri@comptabilite-supmti.ma` → Espace Comptabilité
- [ ] Mot de passe `password123` pour tous

## 🚨 Tests de Sécurité Avancés

### 1. Tentatives de Hack
- [ ] URL manipulation (accès direct à tâche d'autrui) bloquée
- [ ] CSRF protection sur toutes les actions POST/PATCH
- [ ] XSS protection (input/output échappés)

### 2. Logs de Sécurité
- [ ] Accès worker loggé avec IP et user-agent
- [ ] Tentatives d'accès non autorisé loggées
- [ ] Pas de leak d'informations sensibles dans logs

## 📱 Tests Cross-Browser

- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari (si Mac disponible)
- [ ] Mobile Chrome/Firefox

## 🔄 Tests d'Intégration

### 1. Workflow Complet
- [ ] Admin crée espace → Travailleur → Attribution
- [ ] Travailleur se connecte → Voit sa tâche
- [ ] Travailleur change statut → Sauvegarde DB
- [ ] Admin voit changement dans interface admin

### 2. Cas d'Erreur
- [ ] Email malformé → Erreur claire
- [ ] Espace inexistant → Message approprié
- [ ] Connexion réseau perdue → Gestion gracieuse

---

## 🎯 Critères de Réussite

Le système est considéré comme **fonctionnel** si :

✅ **Sécurité** : Isolation complète des données entre travailleurs  
✅ **Fonctionnalité** : Toutes les features principales fonctionnent  
✅ **UX** : Interface intuitive et responsive  
✅ **Performance** : Temps de réponse < 2s pour actions courantes  
✅ **Fiabilité** : Pas de crashs ou erreurs non gérées

---

## 🚀 Commandes de Test Rapide

```bash
# 1. Lancer les migrations
php artisan migrate

# 2. Créer les données de test
php artisan db:seed --class=WorkerTestSeeder

# 3. Lancer le serveur
php artisan serve

# 4. Tester les connexions :
# - Admin : (utiliser compte admin existant)
# - Worker : ahmedbenali@informatique-supmti.ma / password123
```

---

**🎉 Si tous les tests passent, le système est prêt pour la production !**