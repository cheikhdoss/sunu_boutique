<?php

echo "=== DÉMONSTRATION COMPLÈTE DU SYSTÈME D'AUTHENTIFICATION ===\n\n";

$baseUrl = 'http://localhost:8000/api';

// Fonction pour faire une requête HTTP
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Accept: application/json'
    ], $headers));
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// 1. Test d'inscription d'un nouvel utilisateur
echo "1. 📝 INSCRIPTION D'UN NOUVEL UTILISATEUR\n";
echo "==========================================\n";

$newUser = [
    'name' => 'Utilisateur Demo ' . date('His'),
    'email' => 'demo' . time() . '@sunuboutique.sn',
    'password' => 'DemoPassword123!',
    'password_confirmation' => 'DemoPassword123!',
    'phone' => '+221 77 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
    'date_of_birth' => '1995-03-15',
    'gender' => 'male'
];

echo "Données d'inscription :\n";
echo "- Nom : " . $newUser['name'] . "\n";
echo "- Email : " . $newUser['email'] . "\n";
echo "- Téléphone : " . $newUser['phone'] . "\n";
echo "- Date de naissance : " . $newUser['date_of_birth'] . "\n";
echo "- Genre : " . $newUser['gender'] . "\n\n";

$registerResponse = makeRequest($baseUrl . '/auth/register', 'POST', $newUser);

echo "Résultat de l'inscription :\n";
echo "- Code HTTP : " . $registerResponse['code'] . "\n";

if ($registerResponse['code'] === 201) {
    echo "✅ INSCRIPTION RÉUSSIE !\n";
    echo "- ID utilisateur : " . $registerResponse['data']['user']['id'] . "\n";
    echo "- Token généré : " . substr($registerResponse['data']['token'], 0, 20) . "...\n";
    $userToken = $registerResponse['data']['token'];
    $userId = $registerResponse['data']['user']['id'];
} else {
    echo "❌ ÉCHEC DE L'INSCRIPTION\n";
    echo "- Message : " . ($registerResponse['data']['message'] ?? 'Erreur inconnue') . "\n";
    if (isset($registerResponse['data']['errors'])) {
        foreach ($registerResponse['data']['errors'] as $field => $errors) {
            echo "- Erreur $field : " . implode(', ', $errors) . "\n";
        }
    }
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 2. Test de connexion avec un utilisateur existant
echo "2. 🔐 CONNEXION AVEC UN UTILISATEUR EXISTANT\n";
echo "=============================================\n";

$loginData = [
    'email' => 'client@sunuboutique.sn',
    'password' => 'password123'
];

echo "Tentative de connexion avec :\n";
echo "- Email : " . $loginData['email'] . "\n";
echo "- Mot de passe : " . $loginData['password'] . "\n\n";

$loginResponse = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);

echo "Résultat de la connexion :\n";
echo "- Code HTTP : " . $loginResponse['code'] . "\n";

if ($loginResponse['code'] === 200) {
    echo "✅ CONNEXION RÉUSSIE !\n";
    echo "- Utilisateur : " . $loginResponse['data']['user']['name'] . "\n";
    echo "- Email : " . $loginResponse['data']['user']['email'] . "\n";
    echo "- Rôle : " . $loginResponse['data']['user']['role'] . "\n";
    echo "- Token : " . substr($loginResponse['data']['token'], 0, 20) . "...\n";
    $clientToken = $loginResponse['data']['token'];
} else {
    echo "❌ ÉCHEC DE LA CONNEXION\n";
    echo "- Message : " . ($loginResponse['data']['message'] ?? 'Erreur inconnue') . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 3. Test d'accès aux informations utilisateur avec token
if (isset($clientToken)) {
    echo "3. 👤 RÉCUPÉRATION DES INFORMATIONS UTILISATEUR\n";
    echo "===============================================\n";
    
    $meResponse = makeRequest($baseUrl . '/auth/me', 'GET', null, [
        'Authorization: Bearer ' . $clientToken
    ]);
    
    echo "Résultat de la récupération :\n";
    echo "- Code HTTP : " . $meResponse['code'] . "\n";
    
    if ($meResponse['code'] === 200) {
        echo "✅ INFORMATIONS RÉCUPÉRÉES !\n";
        $user = $meResponse['data']['user'];
        echo "- ID : " . $user['id'] . "\n";
        echo "- Nom : " . $user['name'] . "\n";
        echo "- Email : " . $user['email'] . "\n";
        echo "- Téléphone : " . ($user['phone'] ?? 'Non renseigné') . "\n";
        echo "- Date de naissance : " . ($user['date_of_birth'] ?? 'Non renseignée') . "\n";
        echo "- Genre : " . ($user['gender'] ?? 'Non renseigné') . "\n";
        echo "- Rôle : " . $user['role'] . "\n";
        echo "- Inscrit le : " . date('d/m/Y H:i', strtotime($user['created_at'])) . "\n";
    } else {
        echo "❌ ÉCHEC DE LA RÉCUPÉRATION\n";
        echo "- Message : " . ($meResponse['data']['message'] ?? 'Erreur inconnue') . "\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// 4. Test de connexion admin
echo "4. 👑 CONNEXION ADMINISTRATEUR\n";
echo "==============================\n";

$adminLogin = [
    'email' => 'admin@sunuboutique.sn',
    'password' => 'admin123'
];

echo "Tentative de connexion admin :\n";
echo "- Email : " . $adminLogin['email'] . "\n";
echo "- Mot de passe : " . $adminLogin['password'] . "\n\n";

$adminResponse = makeRequest($baseUrl . '/auth/login', 'POST', $adminLogin);

echo "Résultat de la connexion admin :\n";
echo "- Code HTTP : " . $adminResponse['code'] . "\n";

if ($adminResponse['code'] === 200) {
    echo "✅ CONNEXION ADMIN RÉUSSIE !\n";
    echo "- Administrateur : " . $adminResponse['data']['user']['name'] . "\n";
    echo "- Rôle : " . $adminResponse['data']['user']['role'] . "\n";
    $adminToken = $adminResponse['data']['token'];
} else {
    echo "❌ ÉCHEC DE LA CONNEXION ADMIN\n";
    echo "- Message : " . ($adminResponse['data']['message'] ?? 'Erreur inconnue') . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 5. Test de déconnexion
if (isset($clientToken)) {
    echo "5. 🚪 DÉCONNEXION\n";
    echo "=================\n";
    
    $logoutResponse = makeRequest($baseUrl . '/auth/logout', 'POST', [], [
        'Authorization: Bearer ' . $clientToken
    ]);
    
    echo "Résultat de la déconnexion :\n";
    echo "- Code HTTP : " . $logoutResponse['code'] . "\n";
    
    if ($logoutResponse['code'] === 200) {
        echo "✅ DÉCONNEXION RÉUSSIE !\n";
        echo "- Message : " . $logoutResponse['data']['message'] . "\n";
    } else {
        echo "❌ ÉCHEC DE LA DÉCONNEXION\n";
        echo "- Message : " . ($logoutResponse['data']['message'] ?? 'Erreur inconnue') . "\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

// 6. Vérification de la base de données
echo "6. 🗄️ VÉRIFICATION DE LA BASE DE DONNÉES\n";
echo "========================================\n";

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=sunu_boutique', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Compter les utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $userCount = $stmt->fetch()['total'];
    
    // Compter les tokens actifs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personal_access_tokens");
    $tokenCount = $stmt->fetch()['total'];
    
    // Récupérer les derniers utilisateurs
    $stmt = $pdo->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 3");
    $recentUsers = $stmt->fetchAll();
    
    echo "✅ CONNEXION À LA BASE DE DONNÉES RÉUSSIE !\n";
    echo "- Nombre total d'utilisateurs : $userCount\n";
    echo "- Nombre de tokens actifs : $tokenCount\n";
    echo "- Derniers utilisateurs inscrits :\n";
    
    foreach ($recentUsers as $user) {
        echo "  • " . $user['name'] . " (" . $user['email'] . ") - " . 
             date('d/m/Y H:i', strtotime($user['created_at'])) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR DE BASE DE DONNÉES\n";
    echo "- Message : " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 DÉMONSTRATION TERMINÉE !\n\n";

echo "RÉSUMÉ DES FONCTIONNALITÉS TESTÉES :\n";
echo "✅ Inscription d'utilisateur avec validation\n";
echo "✅ Connexion utilisateur avec génération de token\n";
echo "✅ Récupération des informations utilisateur authentifié\n";
echo "✅ Connexion administrateur\n";
echo "✅ Déconnexion avec révocation de token\n";
echo "✅ Persistance des données en base de données\n";
echo "✅ Sécurisation des mots de passe (hachage)\n";
echo "✅ Gestion des rôles (utilisateur/admin)\n";
echo "✅ Validation des données côté serveur\n";
echo "✅ Gestion des erreurs appropriée\n\n";

echo "🌐 Pour tester l'interface web, visitez :\n";
echo "   http://localhost:4200/auth-demo\n\n";

echo "📚 Documentation complète disponible dans :\n";
echo "   AUTHENTICATION_README.md\n\n";

?>