# Configuration Gmail SMTP pour SunuBoutique

## 🔐 Étapes pour configurer Gmail SMTP

### 1. Activer l'authentification à 2 facteurs
1. Allez sur [myaccount.google.com](https://myaccount.google.com)
2. Cliquez sur "Sécurité" dans le menu de gauche
3. Activez "Validation en 2 étapes" si ce n'est pas déjà fait

### 2. Générer un mot de passe d'application
1. Dans la section "Sécurité", cliquez sur "Mots de passe des applications"
2. Sélectionnez "Autre (nom personnalisé)"
3. Tapez "SunuBoutique" comme nom
4. Cliquez sur "Générer"
5. **Copiez le mot de passe généré** (16 caractères)

### 3. Mettre à jour le fichier .env
Remplacez `your_app_password_here` dans le fichier `.env` par le mot de passe d'application généré :

```env
MAIL_PASSWORD=abcd efgh ijkl mnop
```

### 4. Tester la configuration
Exécutez cette commande pour tester l'envoi d'email :

```bash
cd /home/asspro/sunu_boutique/backend
php artisan test:email
```

## 📧 Configuration actuelle

- **SMTP Host:** smtp.gmail.com
- **Port:** 587
- **Encryption:** TLS
- **Username:** comedie442@gmail.com
- **From Address:** comedie442@gmail.com
- **From Name:** SunuBoutique

## 🧪 Test de l'email

Une fois configuré, vous pouvez tester avec :

```bash
# Tester avec la dernière commande
php artisan test:email

# Tester avec une commande spécifique
php artisan test:email 25
```

## 🚨 Dépannage

### Erreur "Authentication failed"
- Vérifiez que l'authentification à 2 facteurs est activée
- Régénérez un nouveau mot de passe d'application
- Assurez-vous d'utiliser le mot de passe d'application et non votre mot de passe Gmail

### Erreur "Connection timeout"
- Vérifiez votre connexion internet
- Assurez-vous que le port 587 n'est pas bloqué par votre firewall

### Email non reçu
- Vérifiez le dossier spam/courrier indésirable
- Vérifiez les logs Laravel : `tail -f storage/logs/laravel.log`

## 📋 Fonctionnalités email

✅ **Email de confirmation de paiement**
- Envoyé automatiquement quand PayDunya confirme le paiement
- Contient les détails de la commande
- Lien vers la page de succès
- Informations de livraison

✅ **Template responsive**
- Design professionnel
- Compatible mobile
- Couleurs de la marque SunuBoutique

✅ **Informations complètes**
- Numéro de commande
- Articles commandés
- Montant total
- Prochaines étapes
- Informations de contact