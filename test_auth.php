<?php

// Script de test pour l'API d'authentification
$baseUrl = 'http://localhost:8000/api';

echo "=== Test de l'API d'authentification ===\n\n";

// Test 1: Inscription d'un nouvel utilisateur
echo "1. Test d'inscription...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'Password123!',
    'password_confirmation' => 'Password123!',
    'phone' => '+221 77 123 45 67',
    'date_of_birth' => '1995-06-15',
    'gender' => 'male'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($registerData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code de réponse: $httpCode\n";
echo "Réponse: " . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Connexion avec un utilisateur existant
echo "2. Test de connexion...\n";
$loginData = [
    'email' => 'client@sunuboutique.sn',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code de réponse: $httpCode\n";
$loginResponse = json_decode($response, true);
echo "Réponse: " . json_encode($loginResponse, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Accès aux informations utilisateur avec token
if (isset($loginResponse['token'])) {
    echo "3. Test d'accès aux informations utilisateur...\n";
    $token = $loginResponse['token'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/me');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Code de réponse: $httpCode\n";
    echo "Réponse: " . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 4: Déconnexion
    echo "4. Test de déconnexion...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/logout');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Code de réponse: $httpCode\n";
    echo "Réponse: " . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "\n\n";
}

echo "=== Tests terminés ===\n";