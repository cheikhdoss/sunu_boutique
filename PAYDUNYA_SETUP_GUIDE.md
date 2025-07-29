# 🚀 Guide de Configuration PayDunya

## 📋 Étapes de configuration

### 1. Créer un compte PayDunya
1. Allez sur [PayDunya.com](https://paydunya.com)
2. Créez un compte marchand
3. Vérifiez votre email et complétez votre profil

### 2. Obtenir les clés API
1. Connectez-vous à votre tableau de bord PayDunya
2. Allez dans **Paramètres** > **API**
3. Copiez vos clés :
   - **Master Key**
   - **Private Key** 
   - **Public Key**

### 3. Configurer le fichier .env
Ouvrez `backend/.env` et remplacez :

```bash
# PayDunya Payment Configuration
PAYDUNYA_MASTER_KEY=votre_master_key_ici
PAYDUNYA_PRIVATE_KEY=votre_private_key_ici
PAYDUNYA_PUBLIC_KEY=votre_public_key_ici
PAYDUNYA_MODE=test  # ou 'live' pour la production
```

### 4. Démarrer avec Ngrok
```bash
# Rendre le script exécutable
chmod +x start-dev-with-ngrok.sh

# Démarrer l'environnement
./start-dev-with-ngrok.sh
```

### 5. Configurer les Webhooks PayDunya
Une fois ngrok démarré, vous obtiendrez une URL comme `https://abc123.ngrok.io`

Dans votre tableau de bord PayDunya :
1. Allez dans **Paramètres** > **Webhooks**
2. Configurez l'URL IPN : `https://abc123.ngrok.io/api/payments/paydunya/ipn`
3. Activez les notifications pour :
   - ✅ Paiement réussi
   - ✅ Paiement échoué
   - ✅ Paiement en attente

## 🧪 Test de l'intégration

### 1. Tester un paiement
1. Ajoutez des produits au panier
2. Procédez au checkout
3. Sélectionnez "PayDunya"
4. Vous serez redirigé vers PayDunya
5. Utilisez les données de test PayDunya

### 2. Données de test PayDunya
```
Carte de test:
- Numéro: 4111 1111 1111 1111
- Expiration: 12/25
- CVV: 123

Mobile Money test:
- Orange Money: +221 77 XXX XX XX
- MTN: +221 76 XXX XX XX
```

### 3. Vérifier les logs
```bash
# Logs Laravel
tail -f backend/storage/logs/laravel.log

# Logs PayDunya spécifiques
grep "PayDunya" backend/storage/logs/laravel.log
```

## 🔧 Dépannage

### Problème : IPN non reçu
**Solution :**
1. Vérifiez que ngrok fonctionne : `curl https://votre-url.ngrok.io/api/payments/paydunya/ipn`
2. Vérifiez les logs PayDunya dans le tableau de bord
3. Testez manuellement l'endpoint IPN

### Problème : Erreur d'authentification
**Solution :**
1. Vérifiez vos clés API dans `.env`
2. Assurez-vous d'utiliser les bonnes clés (test vs live)
3. Redémarrez Laravel après modification : `php artisan config:clear`

### Problème : Redirection échoue
**Solution :**
1. Vérifiez que `APP_FRONTEND_URL` est correct dans `.env`
2. Vérifiez que les URLs de callback sont bien configurées

## 📊 Monitoring

### Dashboard PayDunya
- Transactions en temps réel
- Statistiques de paiement
- Logs des webhooks

### Logs application
```bash
# Voir les dernières transactions
grep "PayDunya" backend/storage/logs/laravel.log | tail -20

# Voir les erreurs
grep "ERROR" backend/storage/logs/laravel.log | grep "PayDunya"
```

## 🚀 Passage en production

### 1. Changer le mode
```bash
PAYDUNYA_MODE=live
```

### 2. Utiliser les vraies clés
Remplacez par vos clés de production PayDunya

### 3. Configurer les vraies URLs
```bash
APP_URL=https://votre-domaine.com
APP_FRONTEND_URL=https://votre-domaine.com
```

### 4. Mettre à jour les webhooks
URL IPN production : `https://votre-domaine.com/api/payments/paydunya/ipn`

## 📞 Support

- **Documentation PayDunya :** [docs.paydunya.com](https://docs.paydunya.com)
- **Support PayDunya :** support@paydunya.com
- **Statut API :** [status.paydunya.com](https://status.paydunya.com)

---

✅ **Votre intégration PayDunya est maintenant prête !**