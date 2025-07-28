# Système d'Authentification JWT - Sunu Boutique

## Vue d'ensemble

Ce système d'authentification utilise Laravel Sanctum côté backend et Angular côté frontend pour fournir une authentification sécurisée basée sur des tokens JWT.

## Backend (Laravel)

### Configuration

1. **Sanctum installé et configuré**
   - Package Laravel Sanctum installé
   - Migrations exécutées
   - Configuration CORS mise à jour

2. **Modèle User étendu**
   - Champs ajoutés : `phone`, `date_of_birth`, `gender`, `avatar`, `is_admin`
   - Trait `HasApiTokens` ajouté
   - Relations définies

### Endpoints API

#### Authentification publique
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion
- `POST /api/auth/forgot-password` - Demande de réinitialisation
- `POST /api/auth/reset-password` - Réinitialisation du mot de passe

#### Authentification protégée (nécessite token)
- `POST /api/auth/logout` - Déconnexion
- `POST /api/auth/logout-all` - Déconnexion de tous les appareils
- `GET /api/auth/me` - Informations utilisateur
- `POST /api/auth/refresh` - Rafraîchir le token
- `GET /api/auth/check-token` - Vérifier la validité du token
- `PUT /api/auth/change-password` - Changer le mot de passe

#### Gestion du profil
- `GET /api/user/profile` - Obtenir le profil
- `PUT /api/user/profile` - Mettre à jour le profil
- `POST /api/user/avatar` - Upload d'avatar
- `DELETE /api/user/avatar` - Supprimer l'avatar
- `DELETE /api/user/account` - Supprimer le compte

### Utilisateurs de test

Deux utilisateurs sont créés par défaut :

1. **Admin**
   - Email: `admin@sunuboutique.sn`
   - Mot de passe: `admin123`
   - Rôle: Administrateur

2. **Client**
   - Email: `client@sunuboutique.sn`
   - Mot de passe: `password123`
   - Rôle: Client

## Frontend (Angular)

### Services

#### AuthService
- Gestion de l'authentification
- Stockage des tokens
- Gestion de l'état utilisateur
- Méthodes pour toutes les opérations d'auth

#### UserService
- Gestion du profil utilisateur
- Upload d'avatar
- Gestion des adresses, commandes, favoris

### Guards

#### AuthGuard
- Protège les routes nécessitant une authentification
- Redirige vers `/auth/login` si non connecté

#### GuestGuard
- Protège les routes d'authentification
- Redirige vers `/` si déjà connecté

#### AdminGuard
- Protège les routes admin
- Vérifie le rôle administrateur

### Intercepteurs

#### AuthInterceptor
- Ajoute automatiquement le token Bearer aux requêtes HTTP
- Gère l'authentification transparente

### Composants

#### LoginComponent
- Formulaire de connexion
- Validation des champs
- Gestion des erreurs
- Option "Se souvenir de moi"

#### RegisterComponent
- Formulaire d'inscription en 2 étapes
- Validation avancée
- Indicateur de force du mot de passe
- Acceptation des conditions

#### ProfileComponent
- Gestion du profil utilisateur
- Upload d'avatar
- Changement de mot de passe
- Statistiques utilisateur

## Utilisation

### Démarrage du backend
```bash
cd backend
php artisan serve
```

### Démarrage du frontend
```bash
cd frontend
npm start
```

### Test de l'authentification

1. **Inscription**
   - Aller sur `/auth/register`
   - Remplir le formulaire en 2 étapes
   - Validation automatique et connexion

2. **Connexion**
   - Aller sur `/auth/login`
   - Utiliser les comptes de test ou créer un nouveau compte
   - Option "Se souvenir de moi" disponible

3. **Profil**
   - Aller sur `/profile` (nécessite d'être connecté)
   - Modifier les informations personnelles
   - Changer le mot de passe
   - Upload d'avatar

### Sécurité

- **Tokens JWT** : Authentification stateless
- **Validation côté serveur** : Toutes les données sont validées
- **Hachage des mots de passe** : Utilisation de bcrypt
- **Protection CSRF** : Sanctum gère automatiquement
- **Validation des fichiers** : Upload d'avatar sécurisé
- **Guards Angular** : Protection des routes côté client

### Gestion des erreurs

- Messages d'erreur localisés en français
- Gestion des erreurs de validation
- Feedback utilisateur avec snackbars
- Gestion des erreurs réseau

## Structure des données

### User Model
```php
{
    "id": 1,
    "name": "Nom Utilisateur",
    "email": "user@example.com",
    "phone": "+221 77 123 45 67",
    "date_of_birth": "1990-01-01",
    "gender": "male|female|other",
    "avatar": "filename.jpg",
    "is_admin": false,
    "email_verified_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
}
```

### Réponse d'authentification
```json
{
    "success": true,
    "message": "Connexion réussie",
    "user": { /* User object */ },
    "token": "jwt_token_here",
    "token_type": "Bearer"
}
```

## Personnalisation

### Ajouter de nouveaux champs utilisateur
1. Créer une migration
2. Mettre à jour le modèle User
3. Modifier les validations dans AuthController
4. Mettre à jour les formulaires Angular

### Ajouter de nouvelles routes protégées
1. Ajouter la route dans `routes/api.php`
2. Utiliser le middleware `auth:sanctum`
3. Ajouter le guard Angular si nécessaire

### Personnaliser les messages d'erreur
- Backend : Modifier les messages dans les contrôleurs
- Frontend : Modifier les messages dans les composants

## Dépannage

### Problèmes courants

1. **CORS** : Vérifier la configuration dans `config/cors.php`
2. **Tokens** : Vérifier que l'intercepteur est bien configuré
3. **Routes** : Vérifier que les guards sont appliqués correctement
4. **Base de données** : Vérifier que les migrations sont exécutées

### Logs

- Backend : `storage/logs/laravel.log`
- Frontend : Console du navigateur

Ce système d'authentification est prêt pour la production et peut être étendu selon les besoins spécifiques de l'application.