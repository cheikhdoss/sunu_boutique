# Guide de Résolution des Problèmes de Mise à Jour du Profil

## Problèmes Identifiés et Solutions

### 1. Problème de Token d'Authentification

**Symptômes :**
- Erreur 401 (Non autorisé) lors de la mise à jour du profil
- Message "Session expirée"
- Redirection vers la page de connexion

**Solutions appliquées :**

#### A. Service d'Authentification Amélioré
- ✅ Ajout de la méthode `updateUserData()` pour synchroniser les données utilisateur
- ✅ Ajout de la méthode `refreshToken()` pour renouveler les tokens expirés
- ✅ Ajout de la méthode `checkTokenValidity()` pour vérifier la validité du token

#### B. Intercepteur HTTP Amélioré
- ✅ Gestion automatique des erreurs 401
- ✅ Refresh automatique du token en cas d'expiration
- ✅ Redirection automatique vers la connexion si le refresh échoue

#### C. Contrôleur Backend Amélioré
- ✅ Ajout de la route `/auth/refresh` pour renouveler les tokens
- ✅ Ajout de la route `/auth/check-token` pour vérifier la validité
- ✅ Correction des erreurs de syntaxe dans les routes

### 2. Gestion des Erreurs dans le Composant Profil

**Améliorations apportées :**
- ✅ Gestion spécifique des erreurs 401 (token expiré)
- ✅ Gestion des erreurs 422 (validation)
- ✅ Messages d'erreur plus détaillés
- ✅ Synchronisation des données utilisateur après mise à jour

## Comment Tester la Solution

### 1. Test avec l'Interface Web
1. Ouvrez le fichier `test_profile_update.html` dans votre navigateur
2. Connectez-vous avec : `admin@example.com` / `password`
3. Modifiez les informations du profil
4. Cliquez sur "Mettre à jour le profil"

### 2. Test avec curl (ligne de commande)

```bash
# 1. Connexion
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# 2. Mise à jour du profil (remplacez TOKEN par le token reçu)
curl -X PUT http://localhost:8000/api/user/profile \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TOKEN" \
  -d '{"name":"Nouveau Nom","email":"admin@example.com","phone":"+221 77 123 45 67","gender":"male"}'
```

### 3. Test dans l'Application Angular

1. Démarrez le serveur backend :
```bash
cd /home/asspro/sunu_boutique/backend
php artisan serve --host=0.0.0.0 --port=8000
```

2. Démarrez l'application frontend :
```bash
cd /home/asspro/sunu_boutique/frontend
ng serve
```

3. Naviguez vers la page de profil et testez la mise à jour

## Diagnostic des Erreurs

### Erreur 401 - Non autorisé
**Causes possibles :**
- Token expiré ou invalide
- Token manquant dans les headers
- Utilisateur non connecté

**Solutions :**
- Vérifiez que le token est présent dans localStorage
- Reconnectez-vous si nécessaire
- L'intercepteur devrait gérer automatiquement le refresh

### Erreur 422 - Validation
**Causes possibles :**
- Données invalides (email mal formaté, nom trop court, etc.)
- Champs obligatoires manquants

**Solutions :**
- Vérifiez les règles de validation côté backend
- Assurez-vous que tous les champs obligatoires sont remplis

### Erreur 500 - Erreur serveur
**Causes possibles :**
- Erreur dans le code backend
- Problème de base de données
- Configuration incorrecte

**Solutions :**
- Vérifiez les logs Laravel : `tail -f storage/logs/laravel.log`
- Vérifiez la configuration de la base de données
- Assurez-vous que les migrations sont à jour

## Vérifications Préventives

### 1. Configuration Backend
```bash
# Vérifier que le serveur fonctionne
curl http://localhost:8000/api/categories

# Vérifier les routes
php artisan route:list | grep auth

# Nettoyer le cache
php artisan config:clear
php artisan route:clear
```

### 2. Configuration Frontend
```bash
# Vérifier la configuration de l'environnement
cat src/environments/environment.ts

# Vérifier que l'intercepteur est bien configuré
grep -r "AuthInterceptor" src/app/
```

### 3. Base de Données
```bash
# Vérifier la connexion à la base de données
php artisan tinker --execute="DB::connection()->getPdo();"

# Vérifier qu'il y a des utilisateurs
php artisan tinker --execute="App\Models\User::count();"
```

## Fichiers Modifiés

1. **Frontend :**
   - `src/app/services/auth.service.ts` - Service d'authentification amélioré
   - `src/app/interceptors/auth.interceptor.ts` - Intercepteur HTTP amélioré
   - `src/app/pages/profile/profile.component.ts` - Gestion d'erreurs améliorée

2. **Backend :**
   - `app/Http/Controllers/Api/AuthController.php` - Nouvelles méthodes refresh et check-token
   - `routes/api.php` - Nouvelles routes d'authentification

3. **Test :**
   - `test_profile_update.html` - Interface de test simple

## Prochaines Étapes

Si le problème persiste :

1. **Vérifiez les logs :**
   ```bash
   # Logs Laravel
   tail -f backend/storage/logs/laravel.log
   
   # Logs du serveur web
   tail -f /var/log/nginx/error.log  # ou Apache selon votre configuration
   ```

2. **Activez le mode debug :**
   ```bash
   # Dans backend/.env
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```

3. **Testez avec Postman ou un autre client REST** pour isoler si le problème vient du frontend ou du backend.

4. **Vérifiez la console du navigateur** pour les erreurs JavaScript.

La solution mise en place devrait résoudre la plupart des problèmes liés aux tokens d'authentification lors de la mise à jour du profil.