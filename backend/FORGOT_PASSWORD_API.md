# API de Réinitialisation de Mot de Passe

Cette documentation explique comment utiliser l'API de réinitialisation de mot de passe avec code de vérification.

## Processus de Réinitialisation

Le processus se déroule en 3 étapes :

1. **Demande de code** : L'utilisateur saisit son email
2. **Vérification du code** : L'utilisateur saisit le code reçu par email
3. **Réinitialisation** : L'utilisateur définit son nouveau mot de passe

## Endpoints

### 1. Demander un code de réinitialisation

**POST** `/api/auth/forgot-password`

**Body:**
```json
{
    "email": "user@example.com"
}
```

**Réponse de succès (200):**
```json
{
    "message": "Un code de vérification a été envoyé à votre adresse email"
}
```

**Réponse d'erreur (404):**
```json
{
    "message": "Aucun compte trouvé avec cette adresse email"
}
```

### 2. Vérifier le code de réinitialisation

**POST** `/api/auth/verify-reset-code`

**Body:**
```json
{
    "email": "user@example.com",
    "code": "123456"
}
```

**Réponse de succès (200):**
```json
{
    "message": "Code vérifié avec succès",
    "reset_token": "abcdef123456789..."
}
```

**Réponse d'erreur (400):**
```json
{
    "message": "Code invalide ou expiré"
}
```

### 3. Réinitialiser le mot de passe

**POST** `/api/auth/reset-password`

**Body:**
```json
{
    "reset_token": "abcdef123456789...",
    "password": "nouveaumotdepasse",
    "password_confirmation": "nouveaumotdepasse"
}
```

**Réponse de succès (200):**
```json
{
    "message": "Mot de passe réinitialisé avec succès"
}
```

**Réponse d'erreur (400):**
```json
{
    "message": "Token invalide ou expiré"
}
```

## Exemple d'utilisation avec JavaScript

```javascript
// Étape 1: Demander un code
async function requestResetCode(email) {
    const response = await fetch('/api/auth/forgot-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email })
    });
    
    const data = await response.json();
    console.log(data.message);
}

// Étape 2: Vérifier le code
async function verifyResetCode(email, code) {
    const response = await fetch('/api/auth/verify-reset-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, code })
    });
    
    const data = await response.json();
    if (response.ok) {
        return data.reset_token;
    } else {
        throw new Error(data.message);
    }
}

// Étape 3: Réinitialiser le mot de passe
async function resetPassword(resetToken, password, passwordConfirmation) {
    const response = await fetch('/api/auth/reset-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reset_token: resetToken,
            password: password,
            password_confirmation: passwordConfirmation
        })
    });
    
    const data = await response.json();
    console.log(data.message);
}

// Utilisation complète
async function fullResetProcess() {
    try {
        // 1. Demander le code
        await requestResetCode('user@example.com');
        
        // 2. L'utilisateur reçoit le code par email et le saisit
        const userCode = prompt('Entrez le code reçu par email:');
        const resetToken = await verifyResetCode('user@example.com', userCode);
        
        // 3. L'utilisateur saisit son nouveau mot de passe
        const newPassword = prompt('Nouveau mot de passe:');
        const confirmPassword = prompt('Confirmez le mot de passe:');
        
        await resetPassword(resetToken, newPassword, confirmPassword);
        
        console.log('Mot de passe réinitialisé avec succès!');
    } catch (error) {
        console.error('Erreur:', error.message);
    }
}
```

## Sécurité

- Le code de vérification expire après **15 minutes**
- Le code est composé de **6 chiffres** générés aléatoirement
- Le token de réinitialisation expire après **15 minutes**
- Tous les tokens d'authentification existants sont révoqués après la réinitialisation
- Les codes et tokens sont stockés de manière sécurisée

## Configuration Email

Assurez-vous que la configuration email est correcte dans le fichier `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=birakanembodj01@gmail.com
MAIL_PASSWORD="vuaegpasembqnvpp"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=birakanembodj01@gmail.com
MAIL_FROM_NAME="SunuBoutique"
```

## Test avec cURL

```bash
# 1. Demander un code
curl -X POST http://localhost:8000/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'

# 2. Vérifier le code
curl -X POST http://localhost:8000/api/auth/verify-reset-code \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","code":"123456"}'

# 3. Réinitialiser le mot de passe
curl -X POST http://localhost:8000/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{"reset_token":"your_token_here","password":"newpassword","password_confirmation":"newpassword"}'
```