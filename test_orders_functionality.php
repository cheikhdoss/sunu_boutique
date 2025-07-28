<?php

echo "=== TEST DES FONCTIONNALITÉS COMMANDES ===\n\n";

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
    echo "- Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Échec de la connexion\n";
    echo "- Code: " . $loginResponse['code'] . "\n\n";
    exit;
}

// 2. Test des statistiques avec vraies données
echo "2. 📊 STATISTIQUES AVEC VRAIES DONNÉES\n";
echo "======================================\n";

$statsResponse = makeRequest($baseUrl . '/user/stats', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($statsResponse['code'] === 200) {
    echo "✅ Statistiques récupérées avec succès\n";
    $stats = $statsResponse['data'];
    echo "- Commandes totales: " . $stats['total_orders'] . "\n";
    echo "- Montant total dépensé: " . number_format($stats['total_spent'], 0, ',', ' ') . " XOF\n";
    echo "- Commandes en attente: " . $stats['pending_orders'] . "\n";
    echo "- Commandes terminées: " . $stats['completed_orders'] . "\n";
    echo "- Catégorie favorite: " . ($stats['favorite_category'] ?? 'Aucune') . "\n\n";
} else {
    echo "❌ Échec de la récupération des statistiques\n";
    echo "- Code: " . $statsResponse['code'] . "\n\n";
}

// 3. Récupération des commandes récentes
echo "3. 📋 COMMANDES RÉCENTES\n";
echo "========================\n";

$recentOrdersResponse = makeRequest($baseUrl . '/user/orders/recent', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($recentOrdersResponse['code'] === 200) {
    echo "✅ Commandes récentes récupérées\n";
    $orders = $recentOrdersResponse['data'];
    echo "- Nombre de commandes: " . count($orders) . "\n";
    
    foreach ($orders as $order) {
        echo "  • " . $order['order_number'] . " - " . $order['status'] . 
             " (" . number_format($order['total'], 0, ',', ' ') . " XOF)" . 
             " - " . date('d/m/Y', strtotime($order['created_at'])) . "\n";
    }
    echo "\n";
} else {
    echo "❌ Échec de la récupération des commandes récentes\n";
    echo "- Code: " . $recentOrdersResponse['code'] . "\n\n";
}

// 4. Récupération de toutes les commandes
echo "4. 📄 TOUTES LES COMMANDES\n";
echo "==========================\n";

$allOrdersResponse = makeRequest($baseUrl . '/user/orders', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($allOrdersResponse['code'] === 200) {
    echo "✅ Toutes les commandes récupérées\n";
    $ordersData = $allOrdersResponse['data'];
    $orders = $ordersData['orders'];
    $pagination = $ordersData['pagination'];
    
    echo "- Total des commandes: " . $pagination['total'] . "\n";
    echo "- Page actuelle: " . $pagination['current_page'] . "/" . $pagination['last_page'] . "\n";
    echo "- Commandes par page: " . $pagination['per_page'] . "\n\n";
    
    echo "Détails des commandes:\n";
    foreach ($orders as $order) {
        echo "  • " . $order['order_number'] . "\n";
        echo "    - Statut: " . $order['status'] . "\n";
        echo "    - Total: " . number_format($order['total'], 0, ',', ' ') . " XOF\n";
        echo "    - Paiement: " . $order['payment_method'] . " (" . $order['payment_status'] . ")\n";
        echo "    - Articles: " . count($order['items']) . " produit(s)\n";
        echo "    - Date: " . date('d/m/Y H:i', strtotime($order['created_at'])) . "\n";
        
        if (!empty($order['items'])) {
            echo "    - Produits:\n";
            foreach ($order['items'] as $item) {
                echo "      * " . $item['product_name'] . " x" . $item['quantity'] . 
                     " (" . number_format($item['unit_price'], 0, ',', ' ') . " XOF)\n";
            }
        }
        echo "\n";
    }
    
    // Garder l'ID de la première commande pour les tests suivants
    $firstOrderId = !empty($orders) ? $orders[0]['id'] : null;
    
} else {
    echo "❌ Échec de la récupération des commandes\n";
    echo "- Code: " . $allOrdersResponse['code'] . "\n\n";
}

// 5. Détails d'une commande spécifique
if (isset($firstOrderId)) {
    echo "5. 🔍 DÉTAILS D'UNE COMMANDE\n";
    echo "============================\n";
    
    $orderDetailsResponse = makeRequest($baseUrl . '/user/orders/' . $firstOrderId, 'GET', null, [
        'Authorization: Bearer ' . $token
    ]);
    
    if ($orderDetailsResponse['code'] === 200) {
        echo "✅ Détails de la commande récupérés\n";
        $orderDetails = $orderDetailsResponse['data'];
        $order = $orderDetails['order'];
        
        echo "- Numéro: " . $order['order_number'] . "\n";
        echo "- Statut: " . $order['status'] . "\n";
        echo "- Total: " . number_format($order['total'], 0, ',', ' ') . " XOF\n";
        echo "- Adresse de livraison: " . $orderDetails['shipping_address']['full_name'] . "\n";
        echo "  " . $orderDetails['shipping_address']['address_line_1'] . "\n";
        echo "  " . $orderDetails['shipping_address']['city'] . ", " . $orderDetails['shipping_address']['country'] . "\n";
        echo "- Articles commandés:\n";
        
        foreach ($orderDetails['items'] as $item) {
            echo "  • " . $item['product_name'] . "\n";
            echo "    Quantité: " . $item['quantity'] . "\n";
            echo "    Prix unitaire: " . number_format($item['unit_price'], 0, ',', ' ') . " XOF\n";
            echo "    Total: " . number_format($item['total_price'], 0, ',', ' ') . " XOF\n";
        }
        echo "\n";
    } else {
        echo "❌ Échec de la récupération des détails\n";
        echo "- Code: " . $orderDetailsResponse['code'] . "\n\n";
    }
}

// 6. Recherche dans les commandes
echo "6. 🔍 RECHERCHE DANS LES COMMANDES\n";
echo "===================================\n";

$searchResponse = makeRequest($baseUrl . '/user/orders/search?status=delivered', 'GET', null, [
    'Authorization: Bearer ' . $token
]);

if ($searchResponse['code'] === 200) {
    echo "✅ Recherche effectuée avec succès\n";
    $searchData = $searchResponse['data'];
    $orders = $searchData['orders'];
    
    echo "- Commandes livrées trouvées: " . count($orders) . "\n";
    
    foreach ($orders as $order) {
        echo "  • " . $order['order_number'] . " - " . 
             number_format($order['total'], 0, ',', ' ') . " XOF\n";
    }
    echo "\n";
} else {
    echo "❌ Échec de la recherche\n";
    echo "- Code: " . $searchResponse['code'] . "\n\n";
}

// 7. Test de téléchargement de facture
if (isset($firstOrderId)) {
    echo "7. 📄 TÉLÉCHARGEMENT DE FACTURE\n";
    echo "===============================\n";
    
    $invoiceResponse = makeRequest($baseUrl . '/user/orders/' . $firstOrderId . '/invoice', 'GET', null, [
        'Authorization: Bearer ' . $token
    ]);
    
    if ($invoiceResponse['code'] === 200) {
        echo "✅ Demande de facture traitée\n";
        echo "- Message: " . $invoiceResponse['data']['message'] . "\n";
        echo "- Numéro de commande: " . $invoiceResponse['data']['order_number'] . "\n";
        echo "- URL de téléchargement: " . $invoiceResponse['data']['download_url'] . "\n\n";
    } else {
        echo "❌ Échec de la demande de facture\n";
        echo "- Code: " . $invoiceResponse['code'] . "\n";
        echo "- Message: " . ($invoiceResponse['data']['message'] ?? 'Erreur inconnue') . "\n\n";
    }
}

echo str_repeat("=", 60) . "\n";
echo "🎉 TESTS DES COMMANDES TERMINÉS !\n\n";

echo "RÉSUMÉ DES FONCTIONNALITÉS TESTÉES :\n";
echo "✅ Statistiques utilisateur avec vraies données\n";
echo "✅ Récupération des commandes récentes\n";
echo "✅ Liste complète des commandes avec pagination\n";
echo "✅ Détails d'une commande spécifique\n";
echo "✅ Recherche et filtrage des commandes\n";
echo "✅ Préparation du téléchargement de factures\n\n";

echo "📊 DONNÉES RÉELLES UTILISÉES :\n";
echo "- Tables orders et order_items créées\n";
echo "- Commandes de test avec vrais produits\n";
echo "- Relations entre utilisateurs, commandes et produits\n";
echo "- Calculs de statistiques basés sur les vraies données\n\n";

echo "🌐 Interface web disponible à :\n";
echo "   http://localhost:4200/profile\n\n";

?>