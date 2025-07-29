# 🚀 Configuration PayDunya avec Ngrok pour développement local

## 🔧 Solution 1: Ngrok (Recommandée)

### Installation Ngrok
```bash
# Ubuntu/Debian
sudo snap install ngrok

# macOS
brew install ngrok

# Windows - Télécharger depuis ngrok.com
```

### Configuration
```bash
# 1. Créer un compte sur ngrok.com et obtenir le token
ngrok config add-authtoken YOUR_NGROK_TOKEN

# 2. Exposer votre serveur Laravel (port 8000)
ngrok http 8000

# 3. Ngrok vous donnera une URL comme:
# https://abc123.ngrok.io -> http://localhost:8000
```

### Configuration PayDunya
```php
// Dans votre service PayDunya
$ipn_url = "https://abc123.ngrok.io/api/payments/paydunya/ipn";
$return_url = "https://abc123.ngrok.io/payment/success";
$cancel_url = "https://abc123.ngrok.io/payment/error";
```

## 🔧 Solution 2: Serveo (Gratuit, sans inscription)

```bash
# Exposer le port 8000
ssh -R 80:localhost:8000 serveo.net

# Vous obtiendrez une URL comme:
# https://randomname.serveo.net
```

## 🔧 Solution 3: LocalTunnel

```bash
# Installation
npm install -g localtunnel

# Utilisation
lt --port 8000 --subdomain myapp

# URL: https://myapp.loca.lt
```

## 🔧 Solution 4: Cloudflare Tunnel (Gratuit)

```bash
# Installation
curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared.deb

# Utilisation
cloudflared tunnel --url http://localhost:8000
```

## 📱 Configuration recommandée pour développement

### 1. Script de démarrage automatique
```bash
#!/bin/bash
# start-dev.sh

# Démarrer Laravel
php artisan serve --port=8000 &

# Démarrer Angular
cd frontend && ng serve --port=4200 &

# Démarrer ngrok
ngrok http 8000 --subdomain=sunuboutique-dev

echo "✅ Environnement de développement démarré!"
echo "🌐 Backend: http://localhost:8000"
echo "🎨 Frontend: http://localhost:4200"
echo "🌍 Public URL: https://sunuboutique-dev.ngrok.io"
```

### 2. Variables d'environnement dynamiques
```php
// config/paydunya.php
return [
    'master_key' => env('PAYDUNYA_MASTER_KEY'),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
    'public_key' => env('PAYDUNYA_PUBLIC_KEY'),
    'mode' => env('PAYDUNYA_MODE', 'test'), // test ou live
    'base_url' => env('PAYDUNYA_BASE_URL', 'https://app.paydunya.com'),
    
    // URLs dynamiques selon l'environnement
    'ipn_url' => env('APP_ENV') === 'local' 
        ? env('NGROK_URL', 'https://sunuboutique-dev.ngrok.io') . '/api/payments/paydunya/ipn'
        : env('APP_URL') . '/api/payments/paydunya/ipn',
        
    'return_url' => env('APP_ENV') === 'local'
        ? env('NGROK_URL', 'https://sunuboutique-dev.ngrok.io') . '/payment/success'
        : env('APP_URL') . '/payment/success',
        
    'cancel_url' => env('APP_ENV') === 'local'
        ? env('NGROK_URL', 'https://sunuboutique-dev.ngrok.io') . '/payment/error'
        : env('APP_URL') . '/payment/error',
];
```

### 3. .env pour développement
```bash
# PayDunya Configuration
PAYDUNYA_MASTER_KEY=your_test_master_key
PAYDUNYA_PRIVATE_KEY=your_test_private_key
PAYDUNYA_PUBLIC_KEY=your_test_public_key
PAYDUNYA_MODE=test

# Ngrok URL (à mettre à jour à chaque démarrage)
NGROK_URL=https://sunuboutique-dev.ngrok.io
```

## 🔄 Workflow de développement

### Démarrage quotidien
1. **Lancer ngrok** : `ngrok http 8000 --subdomain=sunuboutique-dev`
2. **Noter l'URL** : Copier l'URL ngrok générée
3. **Mettre à jour .env** : `NGROK_URL=https://nouvelle-url.ngrok.io`
4. **Redémarrer Laravel** : `php artisan config:clear && php artisan serve`

### Test des notifications
```bash
# Tester l'endpoint IPN
curl -X POST https://sunuboutique-dev.ngrok.io/api/payments/paydunya/ipn \
  -H "Content-Type: application/json" \
  -d '{"status": "completed", "transaction_id": "test123"}'
```

## 🛡️ Sécurité en développement

### Validation des IPN
```php
// Dans votre contrôleur IPN
public function handleIPN(Request $request)
{
    // Vérifier que la requête vient bien de PayDunya
    $allowedIPs = [
        '54.154.168.67',
        '54.154.168.68',
        // IPs de PayDunya
    ];
    
    if (app()->environment('local')) {
        // En local, accepter ngrok
        $allowedIPs[] = 'ngrok.io';
    }
    
    // Valider la signature PayDunya
    $signature = $request->header('X-PayDunya-Signature');
    // ... validation
}
```

## 📊 Monitoring des notifications

### Logs détaillés
```php
// Dans votre service PayDunya
Log::channel('paydunya')->info('IPN reçu', [
    'url' => request()->fullUrl(),
    'ip' => request()->ip(),
    'headers' => request()->headers->all(),
    'payload' => request()->all(),
]);
```

### Dashboard de test
```php
// Route pour voir les dernières notifications
Route::get('/admin/paydunya/logs', function() {
    return view('admin.paydunya-logs', [
        'logs' => collect(File::lines(storage_path('logs/paydunya.log')))
            ->reverse()
            ->take(50)
    ]);
});
```

## 🎯 Avantages de chaque solution

| Solution | Avantages | Inconvénients |
|----------|-----------|---------------|
| **Ngrok** | ✅ Stable, HTTPS, sous-domaine fixe | ❌ Payant pour fonctionnalités avancées |
| **Serveo** | ✅ Gratuit, simple | ❌ Pas de sous-domaine fixe |
| **LocalTunnel** | ✅ Gratuit, sous-domaine personnalisé | ❌ Moins stable |
| **Cloudflare** | ✅ Très rapide, fiable | ❌ Configuration plus complexe |

## 🚀 Recommandation

Pour PayDunya, utilisez **Ngrok** avec un sous-domaine fixe :

```bash
# Commande recommandée
ngrok http 8000 --subdomain=sunuboutique-dev --region=eu
```

Cela vous donne une URL stable : `https://sunuboutique-dev.ngrok.io`

## 📝 Checklist avant test

- [ ] Ngrok installé et configuré
- [ ] URL ngrok mise à jour dans .env
- [ ] Laravel redémarré
- [ ] Endpoint IPN accessible
- [ ] Logs activés
- [ ] Clés PayDunya test configurées

Cette approche vous permet de développer et tester PayDunya localement sans publier votre site ! 🎉