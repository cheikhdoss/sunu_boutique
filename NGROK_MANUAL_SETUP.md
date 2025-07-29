# 🌍 Configuration manuelle de Ngrok

## 🚀 Démarrage manuel (étape par étape)

### 1. Démarrer Laravel
```bash
cd backend
php artisan serve --port=8000
```

### 2. Démarrer Angular (nouveau terminal)
```bash
cd frontend
ng serve --port=4200
```

### 3. Démarrer Ngrok (nouveau terminal)
```bash
# Commande correcte pour ngrok
ngrok http 8000
```

### 4. Récupérer l'URL Ngrok
Ngrok va afficher quelque chose comme :
```
Forwarding    https://abc123.ngrok.io -> http://localhost:8000
```

### 5. Mettre à jour le fichier .env
Ouvrez `backend/.env` et modifiez :
```bash
NGROK_URL=https://abc123.ngrok.io
```

### 6. Redémarrer Laravel
```bash
# Dans le terminal Laravel
Ctrl+C
php artisan config:clear
php artisan serve --port=8000
```

## 📋 URLs importantes pour PayDunya

Une fois ngrok démarré avec l'URL `https://abc123.ngrok.io`, configurez dans PayDunya :

- **IPN URL :** `https://abc123.ngrok.io/api/payments/paydunya/ipn`
- **Success URL :** `https://abc123.ngrok.io/payment/success`
- **Error URL :** `https://abc123.ngrok.io/payment/error`

## 🔧 Commandes ngrok utiles

```bash
# Démarrer ngrok sur le port 8000
ngrok http 8000

# Démarrer avec un sous-domaine personnalisé (compte payant)
ngrok http 8000 --subdomain=monapp

# Démarrer avec authentification basique
ngrok http 8000 --basic-auth="user:password"

# Voir le dashboard ngrok
# Ouvrir http://localhost:4040 dans le navigateur
```

## 🧪 Tester l'endpoint IPN

```bash
# Tester que l'endpoint IPN est accessible
curl -X POST https://votre-url.ngrok.io/api/payments/paydunya/ipn \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'
```

## ⚠️ Important

- L'URL ngrok change à chaque redémarrage
- Pensez à mettre à jour vos webhooks PayDunya
- Le compte gratuit ngrok a des limitations de bande passante

## 🔄 Script automatique

Si vous préférez, utilisez le script automatique :
```bash
chmod +x start-dev-with-ngrok.sh
./start-dev-with-ngrok.sh
```