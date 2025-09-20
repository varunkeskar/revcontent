<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get token and config from .env
$accessToken = $_ENV['ACCESS_TOKEN'];

// List of campaign IDs you want to manage
$campaignIds = [
    2453224,
    2448613
];

// Determine whether to turn ON or OFF based on time
$hour = (int) date('G'); // UTC hour
$enabled = ($hour >= 2 && $hour < 5) ? "on" : "off";

// Loop through each campaign and send API request
foreach ($campaignIds as $campaignId) {
    $ch = curl_init('https://api.revcontent.io/stats/api/v1.0/boosts');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "id" => $campaignId,
        "enabled" => $enabled
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "[" . date('Y-m-d H:i:s') . "] ";
    echo "Campaign {$campaignId} → ";
    echo strtoupper($enabled) . " → ";
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
}
