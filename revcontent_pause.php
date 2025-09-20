<?php

// Read the token from Render's environment variables
$accessToken = $_ENV['ACCESS_TOKEN'];

// Campaigns to manage
$campaignIds = [
    2453224,
    2448613
];

// Get current UTC hour
$hour = (int) gmdate('G'); // 0–23 in UTC

// Enable if between 1 AM and 3:59 AM UTC (i.e. 9 PM – 11:59 PM EST)
$enabled = ($hour >= 1 && $hour < 4) ? "on" : "off";

// Process each campaign
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

    // Output for logs
    echo "[" . gmdate('Y-m-d H:i:s') . " UTC] ";
    echo "Campaign {$campaignId} → ";
    echo strtoupper($enabled) . " → ";
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
}
