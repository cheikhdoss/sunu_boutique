# Gestion Améliorée des Tokens d'Authentification

## Vue d'ensemble

Ce document décrit les améliorations apportées à la gestion des tokens d'authentification dans l'application Sunu Boutique. Les tokens n'expirent maintenant que lors d'une déconnexion explicite de l'utilisateur.

## Fonctionnalités Implémentées

### 1. Backend (Laravel Sanctum)

#### AuthController Amélioré
- **Tokens sans expiration** : Les tokens sont créés avec `expires_at = null`
- **Nettoyage automatique** : Suppression des anciens tokens lors de la connexion
- **Déconnexion sélective** : `logout()` pour l'appareil actuel
- **Déconnexion globale** : `logoutAll()` pour tous les appareils
- **Vérification de validité** : `checkToken()` pour valider un token

#### Nouvelles Routes API
```php
POST /api/auth/logout-all    // Déconnecter de tous les appareils
GET  /api/auth/check-token   // Vérifier la validité du token
```

### 2. Frontend (Angular)

#### Service d'Authentification Amélioré
- **Vérification périodique** : Contrôle automatique toutes les 5 minutes
- **Détection de visibilité** : Vérification quand l'utilisateur revient sur l'onglet
- **Gestion des erreurs** : Déconnexion automatique en cas de token invalide
- **Notifications** : Alertes utilisateur pour les problèmes d'authentification

#### Intercepteur HTTP Amélioré
- **Rafraîchissement automatique** : Tentative de renouvellement en cas d'erreur 401
- **Gestion des requêtes simultanées** : Évite les appels multiples de refresh
- **Exclusion des routes d'auth** : Ne traite pas les erreurs sur les routes d'authentification

#### Nouveaux Services
- **VisibilityService** : Détecte quand l'utilisateur change d'onglet
- **NotificationService** : Système de notifications pour l'utilisateur
- **AuthGuard** : Protection des routes avec vérification de token

## Configuration

### Backend
```php
// config/sanctum.php
'expiration' => null, // Tokens sans expiration automatique
```

### Frontend
```typescript
// Vérification périodique toutes les 5 minutes
private tokenCheckInterval = 5 * 60 * 1000;
```

## Flux de Fonctionnement

### 1. Connexion
1. L'utilisateur se connecte
2. Suppression des anciens tokens
3. Création d'un nouveau token sans expiration
4. Démarrage de la vérification périodique

### 2. Vérification Continue
1. **Vérification périodique** : Toutes les 5 minutes
2. **Vérification au retour** : Quand l'utilisateur revient sur l'onglet
3. **Vérification sur erreur** : En cas d'erreur HTTP 401

### 3. Gestion des Erreurs
1. Token invalide détecté
2. Tentative de rafraîchissement automatique
3. Si échec : déconnexion et redirection vers login
4. Notification à l'utilisateur

### 4. Déconnexion
1. **Déconnexion simple** : Supprime le token actuel
2. **Déconnexion globale** : Supprime tous les tokens de l'utilisateur
3. Arrêt de la vérification périodique
4. Nettoyage du localStorage

## Sécurité

### Mesures Implémentées
- **Validation côté serveur** : Chaque requête vérifie la validité du token
- **Nettoyage automatique** : Suppression des tokens orphelins
- **Détection d'inactivité** : Vérification lors du retour sur l'application
- **Rafraîchissement sécurisé** : Renouvellement automatique des tokens

### Bonnes Pratiques
- Les tokens sont stockés uniquement en localStorage (pas de cookies)
- Vérification systématique avant les opérations sensibles
- Déconnexion automatique en cas de problème de sécurité
- Logs des tentatives d'accès non autorisées

## Utilisation

### Côté Frontend
```typescript
// Connexion
this.authService.login(email, password).subscribe();

// Déconnexion simple
this.authService.logout().subscribe();

// Déconnexion de tous les appareils
this.authService.logoutAll().subscribe();

// Vérification manuelle du token
this.authService.checkTokenValidity().subscribe();
```

### Côté Backend
```php
// Dans un contrôleur protégé
$user = $request->user(); // Utilisateur authentifié

// Déconnexion manuelle d'un token spécifique
$request->user()->currentAccessToken()->delete();

// Déconnexion de tous les appareils
$request->user()->tokens()->delete();
```

## Avantages

1. **Sécurité renforcée** : Détection rapide des tokens compromis
2. **Expérience utilisateur** : Pas de déconnexions intempestives
3. **Gestion flexible** : Contrôle granulaire des sessions
4. **Performance** : Vérifications optimisées et non bloquantes
5. **Maintenance** : Nettoyage automatique des tokens obsolètes

## Monitoring

### Métriques à Surveiller
- Nombre de tokens actifs par utilisateur
- Fréquence des rafraîchissements de tokens
- Taux d'échec des vérifications de tokens
- Durée moyenne des sessions utilisateur

### Logs Importants
- Tentatives de connexion avec tokens invalides
- Échecs de rafraîchissement de tokens
- Déconnexions automatiques pour sécurité

## Maintenance

### Tâches Périodiques
- Nettoyage des tokens expirés (si implémenté)
- Analyse des logs de sécurité
- Mise à jour des clés de chiffrement
- Révision des politiques d'expiration

### Commandes Utiles
```bash
# Nettoyer tous les tokens expirés
php artisan sanctum:prune-expired

# Voir les tokens actifs
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::count()
```

## Dépannage

### Problèmes Courants
1. **Token non reconnu** : Vérifier la configuration Sanctum
2. **Déconnexions fréquentes** : Vérifier la connectivité réseau
3. **Erreurs CORS** : Configurer les domaines autorisés
4. **Performance lente** : Optimiser les requêtes de vérification

### Solutions
- Vérifier les logs Laravel et du navigateur
- Tester les endpoints d'authentification manuellement
- Valider la configuration des middlewares
- Contrôler les en-têtes HTTP des requêtes