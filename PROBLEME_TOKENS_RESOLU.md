# Problème avec les Tokens de Connexion - RÉSOLU

## Problème identifié
Le problème avec les tokens de connexion était lié à une mauvaise configuration de Laravel Sanctum pour une API REST pure.

## Corrections apportées

### 1. Configuration Sanctum (Backend)
- **Fichier modifié**: `backend/bootstrap/app.php`
- **Problème**: Le middleware `EnsureFrontendRequestsAreStateful` était activé, ce qui forçait l'utilisation de sessions et CSRF pour une API REST
- **Solution**: Suppression du middleware stateful pour utiliser uniquement les tokens Bearer

### 2. Configuration CORS (Backend)
- **Fichier modifié**: `backend/config/cors.php`
- **Améliorations**:
  - Ajout du support pour `localhost:8080` (serveur de test)
  - Activation de `supports_credentials: true`

### 3. Configuration Sanctum Stateful Domains
- **Fichier modifié**: `backend/config/sanctum.php`
- **Améliorations**: Ajout des ports 4200 et 8080 dans les domaines stateful

### 4. Outil de diagnostic (Frontend)
- **Nouveau fichier**: `frontend/src/app/pages/auth-test/auth-test.component.ts`
- **Fonctionnalité**: Composant de test pour diagnostiquer les problèmes d'authentification
- **Accès**: Menu Aide > Test Auth dans l'application

## Tests de validation

### Backend (API REST)
```bash
# Test de connexion
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "client@sunuboutique.sn",
    "password": "password123"
  }'

# Test avec token
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer [TOKEN_REÇU]"
```

### Frontend (Angular)
1. Accéder à `http://localhost:4200/auth-test`
2. Utiliser les boutons de test pour diagnostiquer
3. Vérifier que tous les tests passent

## Comptes de test disponibles

### Utilisateur client
- **Email**: `client@sunuboutique.sn`
- **Mot de passe**: `password123`

### Utilisateur admin
- **Email**: `admin@sunuboutique.sn`
- **Mot de passe**: `admin123`

### Autres utilisateurs
- **Email**: `amadou@example.com` | **Mot de passe**: `password123`
- **Email**: `fatou@example.com` | **Mot de passe**: `password123`
- **Email**: `ousmane@example.com` | **Mot de passe**: `password123`

## Architecture d'authentification

### Mode de fonctionnement
- **Type**: API REST avec tokens Bearer (Laravel Sanctum)
- **Stockage**: localStorage côté frontend
- **Intercepteur**: Ajout automatique du header Authorization
- **Expiration**: Pas d'expiration automatique (configurable)

### Flux d'authentification
1. **Login**: POST `/api/auth/login` → Retourne user + token
2. **Stockage**: Token sauvé dans localStorage
3. **Requêtes**: Intercepteur ajoute `Authorization: Bearer {token}`
4. **Vérification**: Middleware `auth:sanctum` valide le token
5. **Logout**: POST `/api/auth/logout` + suppression localStorage

## Commandes utiles

### Redémarrer les services
```bash
# Backend
cd backend && php artisan config:clear && php artisan cache:clear

# Frontend (si nécessaire)
cd frontend && npm start
```

### Vérifier les migrations
```bash
cd backend && php artisan migrate:status
```

### Voir les tokens actifs
```bash
cd backend && php artisan tinker
>>> \App\Models\User::find(3)->tokens
```

## Fichiers de test créés
- `test-auth.html` : Page de test HTML simple
- `frontend/src/app/pages/auth-test/auth-test.component.ts` : Composant Angular de diagnostic

Le problème est maintenant résolu et l'authentification fonctionne correctement avec les tokens Bearer.