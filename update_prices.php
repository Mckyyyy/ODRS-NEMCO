<?php
// update_prices.php

header('Content-Type: application/json');

// Path to the JSON file
$pricesFile = 'prices.json';

// Load existing prices
if (file_exists($pricesFile)) {
    $prices = json_decode(file_get_contents($pricesFile), true);
} else {
    $prices = array(
        'forEnrollment' => 75,
        'forBoardExam' => 75,
        'forReference' => 75,
        'forStudiesAbroad' => 75,
        'forTransfer' => 75,
        'forEmployment' => 75,
        'forRanking' => 75,
        'forReissuance' => 150,
        'forGoodmoral' => 30,
        'forGpa' => 30,
        'forEarnedUnits' => 30,
        'forGraduating' => 30,
        'forGraduation' => 30,
        'forSpec' => 30,
        'formediumInstruction' => 30,
        'forCertification' => 60,
        'forAuthentication' => 20,
        'forDismissal' => 60,
        'forEnrollmentcert' => 30,
        'others' => 0
    );
}

// Update prices if POST request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPrices = $_POST['prices'] ?? [];

    foreach ($newPrices as $key => $value) {
        if (array_key_exists($key, $prices)) {
            $prices[$key] = floatval($value); // Update only valid keys
        }
    }

    // Save updated prices to the JSON file
    file_put_contents($pricesFile, json_encode($prices, JSON_PRETTY_PRINT));

    echo json_encode(['status' => 'success', 'message' => 'Prices updated successfully']);
    exit;
}

// Return current prices for GET requests
echo json_encode($prices);
