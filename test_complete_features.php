<?php

echo "=== TEST COMPLET DES FONCTIONNALITÉS COMPTE CLIENT ===\n\n";

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
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// 1. Connexion utilisateur
echo "1. 🔐 CONNEXION UTILISATEUR\n";
echo "===========================\n";

$loginData = [
    'email' => 'client@sunuboutique.sn',
    'password' => 'password123'
];

$loginResponse = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);

if ($loginResponse['code'] === 200) {
    echo "✅ Connexion réussie\n";
    $token = $loginResponse['data']['token'];
    $userId = $loginResponse['data']['user']['id'];
    echo "- Token: " . substr($token, 0, 20) . "...\n";
    echo "- Utilisateur: " . $loginResponse['data']['user']['name'] . "\n\n";
} else {
    echo "❌ Échec de la connexion\n";
    echo "- Code: " . $loginResponse['code'] . "\n";
    echo "- Message: " . ($loginResponse['data']['message'] ?? 'Erreur inconnue') . "\n\n";
    exit;
}

// 2. Récupération du profil
echo "2. 👤 RÉCUPÉRATION DU PROFIL\n";
echo "============================\n";

$profileResponse = makeRequest($baseUrl . '/user/profile', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($profileResponse['code'] === 200) {
    echo "✅ Profil récupéré avec succès\n";
    $profile = $profileResponse['data'];
    echo "- Nom: " . $profile['name'] . "\n";
    echo "- Email: " . $profile['email'] . "\n";
    echo "- Téléphone: " . ($profile['phone'] ?? 'Non renseigné') . "\n";
    echo "- Genre: " . ($profile['gender'] ?? 'Non renseigné') . "\n\n";
} else {
    echo "❌ Échec de la récupération du profil\n";
    echo "- Code: " . $profileResponse['code'] . "\n\n";
}

// 3. Mise à jour du profil
echo "3. ✏️ MISE À JOUR DU PROFIL\n";
echo "===========================\n";

$updateData = [
    'name' => 'Client Test Modifié',
    'email' => 'client@sunuboutique.sn',
    'phone' => '+221 77 999 88 77',
    'date_of_birth' => '1990-05-20',
    'gender' => 'female'
];

$updateResponse = makeRequest($baseUrl . '/user/profile', 'PUT', $updateData, [
    'Authorization: Bearer ' . $token
]);

if ($updateResponse['code'] === 200) {
    echo "✅ Profil mis à jour avec succès\n";
    echo "- Nouveau nom: " . $updateResponse['data']['user']['name'] . "\n";
    echo "- Nouveau téléphone: " . $updateResponse['data']['user']['phone'] . "\n\n";
} else {
    echo "❌ Échec de la mise à jour du profil\n";
    echo "- Code: " . $updateResponse['code'] . "\n";
    if (isset($updateResponse['data']['errors'])) {
        foreach ($updateResponse['data']['errors'] as $field => $errors) {
            echo "- Erreur $field: " . implode(', ', $errors) . "\n";
        }
    }
    echo "\n";
}

// 4. Création d'une adresse
echo "4. 🏠 CRÉATION D'UNE ADRESSE\n";
echo "============================\n";

$addressData = [
    'type' => 'shipping',
    'first_name' => 'Client',
    'last_name' => 'Test',
    'address_line_1' => '123 Rue de la Paix',
    'address_line_2' => 'Appartement 4B',
    'city' => 'Dakar',
    'state' => 'Dakar',
    'postal_code' => '12000',
    'country' => 'Sénégal',
    'phone' => '+221 77 123 45 67',
    'is_default' => true
];

$addressResponse = makeRequest($baseUrl . '/user/addresses', 'POST', $addressData, [
    'Authorization: Bearer ' . $token
]);

if ($addressResponse['code'] === 201) {
    echo "✅ Adresse créée avec succès\n";
    $addressId = $addressResponse['data']['address']['id'];
    echo "- ID: " . $addressId . "\n";
    echo "- Type: " . $addressResponse['data']['address']['type'] . "\n";
    echo "- Adresse: " . $addressResponse['data']['address']['address_line_1'] . "\n";
    echo "- Ville: " . $addressResponse['data']['address']['city'] . "\n";
    echo "- Par défaut: " . ($addressResponse['data']['address']['is_default'] ? 'Oui' : 'Non') . "\n\n";
} else {
    echo "❌ Échec de la création de l'adresse\n";
    echo "- Code: " . $addressResponse['code'] . "\n";
    if (isset($addressResponse['data']['errors'])) {
        foreach ($addressResponse['data']['errors'] as $field => $errors) {
            echo "- Erreur $field: " . implode(', ', $errors) . "\n";
        }
    }
    echo "\n";
}

// 5. Récupération des adresses
echo "5. 📋 RÉCUPÉRATION DES ADRESSES\n";
echo "===============================\n";

$addressesResponse = makeRequest($baseUrl . '/user/addresses', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($addressesResponse['code'] === 200) {
    echo "✅ Adresses récupérées avec succès\n";
    $addresses = $addressesResponse['data'];
    echo "- Nombre d'adresses: " . count($addresses) . "\n";
    
    foreach ($addresses as $address) {
        echo "  • ID: " . $address['id'] . " - " . $address['type'] . 
             " (" . $address['city'] . ")" . 
             ($address['is_default'] ? " [Par défaut]" : "") . "\n";
    }
    echo "\n";
} else {
    echo "❌ Échec de la récupération des adresses\n";
    echo "- Code: " . $addressesResponse['code'] . "\n\n";
}

// 6. Création d'une deuxième adresse
echo "6. 🏢 CRÉATION D'UNE ADRESSE DE FACTURATION\n";
echo "===========================================\n";

$billingAddressData = [
    'type' => 'billing',
    'first_name' => 'Client',
    'last_name' => 'Test',
    'company' => 'Mon Entreprise SARL',
    'address_line_1' => '456 Avenue de l\'Indépendance',
    'city' => 'Thiès',
    'postal_code' => '21000',
    'country' => 'Sénégal',
    'phone' => '+221 77 987 65 43',
    'is_default' => false
];

$billingResponse = makeRequest($baseUrl . '/user/addresses', 'POST', $billingAddressData, [
    'Authorization: Bearer ' . $token
]);

if ($billingResponse['code'] === 201) {
    echo "✅ Adresse de facturation créée avec succès\n";
    $billingAddressId = $billingResponse['data']['address']['id'];
    echo "- ID: " . $billingAddressId . "\n";
    echo "- Type: " . $billingResponse['data']['address']['type'] . "\n";
    echo "- Entreprise: " . $billingResponse['data']['address']['company'] . "\n";
    echo "- Ville: " . $billingResponse['data']['address']['city'] . "\n\n";
} else {
    echo "❌ Échec de la création de l'adresse de facturation\n";
    echo "- Code: " . $billingResponse['code'] . "\n\n";
}

// 7. Modification d'une adresse
if (isset($addressId)) {
    echo "7. ✏️ MODIFICATION D'UNE ADRESSE\n";
    echo "===============================\n";

    $updateAddressData = [
        'type' => 'shipping',
        'first_name' => 'Client',
        'last_name' => 'Test Modifié',
        'address_line_1' => '123 Rue de la Paix Modifiée',
        'address_line_2' => 'Appartement 5C',
        'city' => 'Dakar',
        'state' => 'Dakar',
        'postal_code' => '12001',
        'country' => 'Sénégal',
        'phone' => '+221 77 111 22 33',
        'is_default' => true
    ];

    $updateAddressResponse = makeRequest($baseUrl . '/user/addresses/' . $addressId, 'PUT', $updateAddressData, [
        'Authorization: Bearer ' . $token
    ]);

    if ($updateAddressResponse['code'] === 200) {
        echo "✅ Adresse modifiée avec succès\n";
        echo "- Nouveau nom: " . $updateAddressResponse['data']['address']['last_name'] . "\n";
        echo "- Nouvelle adresse: " . $updateAddressResponse['data']['address']['address_line_1'] . "\n";
        echo "- Nouveau code postal: " . $updateAddressResponse['data']['address']['postal_code'] . "\n\n";
    } else {
        echo "❌ Échec de la modification de l'adresse\n";
        echo "- Code: " . $updateAddressResponse['code'] . "\n\n";
    }
}

// 8. Définir une adresse par défaut
if (isset($billingAddressId)) {
    echo "8. ⭐ DÉFINIR UNE ADRESSE PAR DÉFAUT\n";
    echo "===================================\n";

    $defaultResponse = makeRequest($baseUrl . '/user/addresses/' . $billingAddressId . '/set-default', 'PUT', [], [
        'Authorization: Bearer ' . $token
    ]);

    if ($defaultResponse['code'] === 200) {
        echo "✅ Adresse définie comme par défaut\n";
        echo "- Message: " . $defaultResponse['data']['message'] . "\n\n";
    } else {
        echo "❌ Échec de la définition de l'adresse par défaut\n";
        echo "- Code: " . $defaultResponse['code'] . "\n\n";
    }
}

// 9. Récupération des statistiques utilisateur
echo "9. 📊 RÉCUPÉRATION DES STATISTIQUES\n";
echo "===================================\n";

$statsResponse = makeRequest($baseUrl . '/user/stats', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($statsResponse['code'] === 200) {
    echo "✅ Statistiques récupérées avec succès\n";
    $stats = $statsResponse['data'];
    echo "- Commandes totales: " . $stats['total_orders'] . "\n";
    echo "- Montant total dépensé: " . $stats['total_spent'] . " XOF\n";
    echo "- Commandes en attente: " . $stats['pending_orders'] . "\n";
    echo "- Commandes terminées: " . $stats['completed_orders'] . "\n";
    echo "- Catégorie favorite: " . ($stats['favorite_category'] ?? 'Aucune') . "\n\n";
} else {
    echo "❌ Échec de la récupération des statistiques\n";
    echo "- Code: " . $statsResponse['code'] . "\n\n";
}

// 10. Changement de mot de passe
echo "10. 🔒 CHANGEMENT DE MOT DE PASSE\n";
echo "=================================\n";

$passwordData = [
    'current_password' => 'password123',
    'new_password' => 'nouveaumotdepasse123',
    'new_password_confirmation' => 'nouveaumotdepasse123'
];

$passwordResponse = makeRequest($baseUrl . '/user/change-password', 'PUT', $passwordData, [
    'Authorization: Bearer ' . $token
]);

if ($passwordResponse['code'] === 200) {
    echo "✅ Mot de passe modifié avec succès\n";
    echo "- Message: " . $passwordResponse['data']['message'] . "\n\n";
} else {
    echo "❌ Échec du changement de mot de passe\n";
    echo "- Code: " . $passwordResponse['code'] . "\n";
    echo "- Message: " . ($passwordResponse['data']['message'] ?? 'Erreur inconnue') . "\n\n";
}

// 11. Suppression d'une adresse
if (isset($addressId)) {
    echo "11. 🗑️ SUPPRESSION D'UNE ADRESSE\n";
    echo "===============================\n";

    $deleteResponse = makeRequest($baseUrl . '/user/addresses/' . $addressId, 'DELETE', null, [
        'Authorization: Bearer ' . $token
    ]);

    if ($deleteResponse['code'] === 200) {
        echo "✅ Adresse supprimée avec succès\n";
        echo "- Message: " . $deleteResponse['data']['message'] . "\n\n";
    } else {
        echo "❌ Échec de la suppression de l'adresse\n";
        echo "- Code: " . $deleteResponse['code'] . "\n\n";
    }
}

// 12. Vérification finale des adresses
echo "12. 🔍 VÉRIFICATION FINALE DES ADRESSES\n";
echo "=======================================\n";

$finalAddressesResponse = makeRequest($baseUrl . '/user/addresses', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($finalAddressesResponse['code'] === 200) {
    echo "✅ Vérification finale réussie\n";
    $finalAddresses = $finalAddressesResponse['data'];
    echo "- Nombre d'adresses restantes: " . count($finalAddresses) . "\n";
    
    foreach ($finalAddresses as $address) {
        echo "  • ID: " . $address['id'] . " - " . $address['type'] . 
             " (" . $address['city'] . ")" . 
             ($address['is_default'] ? " [Par défaut]" : "") . "\n";
    }
    echo "\n";
} else {
    echo "❌ Échec de la vérification finale\n";
    echo "- Code: " . $finalAddressesResponse['code'] . "\n\n";
}

// 13. Déconnexion
echo "13. 🚪 DÉCONNEXION\n";
echo "==================\n";

$logoutResponse = makeRequest($baseUrl . '/auth/logout', 'POST', [], [
    'Authorization: Bearer ' . $token
]);

if ($logoutResponse['code'] === 200) {
    echo "✅ Déconnexion réussie\n";
    echo "- Message: " . $logoutResponse['data']['message'] . "\n\n";
} else {
    echo "❌ Échec de la déconnexion\n";
    echo "- Code: " . $logoutResponse['code'] . "\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "🎉 TESTS TERMINÉS !\n\n";

echo "RÉSUMÉ DES FONCTIONNALITÉS TESTÉES :\n";
echo "✅ Connexion utilisateur\n";
echo "✅ Récupération du profil utilisateur\n";
echo "✅ Mise à jour du profil utilisateur\n";
echo "✅ Création d'adresses (livraison et facturation)\n";
echo "✅ Récupération des adresses\n";
echo "✅ Modification d'adresses\n";
echo "✅ Définition d'adresse par défaut\n";
echo "✅ Suppression d'adresses\n";
echo "✅ Récupération des statistiques utilisateur\n";
echo "✅ Changement de mot de passe\n";
echo "✅ Déconnexion\n\n";

echo "🌐 Interface web disponible à :\n";
echo "   http://localhost:4200/profile\n\n";

echo "📚 Documentation complète dans :\n";
echo "   AUTHENTICATION_README.md\n\n";

?>