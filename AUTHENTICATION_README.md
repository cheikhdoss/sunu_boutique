# Système d'Authentification - Sunu Boutique

## Vue d'ensemble

Le système d'authentification de Sunu Boutique est un système complet et fonctionnel qui permet aux utilisateurs de s'inscrire, se connecter, et gérer leur profil. Les données utilisateur sont persistées dans une base de données MySQL via Laravel Sanctum pour l'authentification API.

## Fonctionnalités

### ✅ Fonctionnalités implémentées

1. **Inscription d'utilisateur**
   - Validation des données côté client et serveur
   - Champs : nom, email, mot de passe, téléphone, date de naissance, genre
   - Hachage sécurisé des mots de passe
   - Génération automatique de token d'authentification

2. **Connexion d'utilisateur**
   - Authentification par email/mot de passe
   - Génération de token JWT via Laravel Sanctum
   - Gestion des sessions côté client

3. **Déconnexion**
   - Révocation du token côté serveur
   - Nettoyage des données locales

4. **Gestion des profils**
   - Récupération des informations utilisateur
   - Mise à jour des données personnelles

5. **Sécurité**
   - Protection CSRF
   - Validation des données
   - Hachage des mots de passe avec bcrypt
   - Tokens d'authentification sécurisés

## Architecture

### Backend (Laravel)

#### Modèle User
```php
// app/Models/User.php
- Utilise Laravel Sanctum (HasApiTokens)
- Champs : name, email, password, phone, date_of_birth, gender, is_admin
- Validation et casting automatique des types
```

#### Contrôleur d'authentification
```php
// app/Http/Controllers/Api/AuthController.php
- register() : Inscription d'un nouvel utilisateur
- login() : Connexion utilisateur
- logout() : Déconnexion utilisateur
- me() : Informations de l'utilisateur connecté
- forgotPassword() : Réinitialisation de mot de passe
- resetPassword() : Nouveau mot de passe
```

#### Routes API
```php
// routes/api.php
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout (protégée)
GET  /api/auth/me (protégée)
POST /api/auth/forgot-password
POST /api/auth/reset-password
```

### Frontend (Angular)

#### Service d'authentification
```typescript
// src/app/services/auth.service.ts
- Gestion de l'état de connexion avec BehaviorSubject
- Méthodes : login(), register(), logout(), isAuthenticated()
- Stockage local des tokens et informations utilisateur
```

#### Intercepteur HTTP
```typescript
// src/app/interceptors/auth.interceptor.ts
- Ajout automatique du token Bearer aux requêtes
- Gestion des erreurs d'authentification
```

#### Guard d'authentification
```typescript
// src/app/guards/auth.guard.ts
- Protection des routes nécessitant une authentification
- Redirection automatique vers la page de connexion
```

## Base de données

### Table users
```sql
- id (bigint, primary key)
- name (varchar)
- email (varchar, unique)
- email_verified_at (timestamp, nullable)
- password (varchar, hashed)
- phone (varchar, nullable)
- date_of_birth (date, nullable)
- gender (enum: male, female, other, nullable)
- is_admin (boolean, default: false)
- remember_token (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Table personal_access_tokens (Laravel Sanctum)
```sql
- id (bigint, primary key)
- tokenable_type (varchar)
- tokenable_id (bigint)
- name (varchar)
- token (varchar, hashed)
- abilities (text, nullable)
- last_used_at (timestamp, nullable)
- expires_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Comptes de test

### Administrateur
- **Email :** admin@sunuboutique.sn
- **Mot de passe :** admin123
- **Rôle :** Administrateur

### Client
- **Email :** client@sunuboutique.sn
- **Mot de passe :** password123
- **Rôle :** Utilisateur

### Autres utilisateurs de test
- amadou@example.com / password123
- fatou@example.com / password123
- ousmane@example.com / password123

## Installation et configuration

### 1. Backend (Laravel)

```bash
# Installation des dépendances
cd backend
composer install

# Configuration de la base de données
cp .env.example .env
# Modifier les paramètres de base de données dans .env

# Génération de la clé d'application
php artisan key:generate

# Exécution des migrations
php artisan migrate

# Création des utilisateurs de test
php artisan db:seed --class=UserSeeder

# Démarrage du serveur
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Frontend (Angular)

```bash
# Installation des dépendances
cd frontend
npm install

# Démarrage du serveur de développement
ng serve
```

## Test du système

### 1. Via l'interface web
Accédez à `http://localhost:4200/auth-demo` pour tester toutes les fonctionnalités d'authentification.

### 2. Via l'API directement
```bash
# Test de connexion
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"client@sunuboutique.sn","password":"password123"}'

# Test d'inscription
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Nouveau Utilisateur",
    "email":"nouveau@example.com",
    "password":"password123",
    "password_confirmation":"password123",
    "phone":"+221 77 123 45 67",
    "gender":"male"
  }'
```

### 3. Script de test automatisé
```bash
# Exécution du script de test
php test_auth.php
```

## Sécurité

### Mesures de sécurité implémentées

1. **Hachage des mots de passe** : Utilisation de bcrypt
2. **Validation des données** : Côté client et serveur
3. **Tokens sécurisés** : Laravel Sanctum avec expiration
4. **Protection CSRF** : Activée par défaut
5. **Validation des emails** : Format et unicité
6. **Gestion des erreurs** : Messages d'erreur appropriés sans révéler d'informations sensibles

### Recommandations pour la production

1. **HTTPS obligatoire** : Toutes les communications doivent être chiffrées
2. **Limitation du taux de requêtes** : Prévention des attaques par force brute
3. **Validation côté serveur renforcée** : Validation stricte de tous les inputs
4. **Logs de sécurité** : Enregistrement des tentatives de connexion
5. **Expiration des tokens** : Configuration d'une durée de vie appropriée
6. **Vérification des emails** : Implémentation de la vérification par email

## Maintenance

### Commandes utiles

```bash
# Voir les utilisateurs connectés
php artisan tinker
>>> \App\Models\User::whereNotNull('email_verified_at')->count()

# Révoquer tous les tokens d'un utilisateur
>>> $user = \App\Models\User::find(1);
>>> $user->tokens()->delete();

# Nettoyer les tokens expirés
php artisan sanctum:prune-expired
```

## Support

Pour toute question ou problème concernant le système d'authentification, veuillez consulter :

1. La documentation Laravel Sanctum : https://laravel.com/docs/sanctum
2. La documentation Angular : https://angular.io/guide/http
3. Les logs de l'application : `storage/logs/laravel.log`

---

**Note :** Ce système d'authentification est entièrement fonctionnel et prêt pour la production avec les ajustements de sécurité appropriés.