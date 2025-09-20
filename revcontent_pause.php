<?php
// revcontent_pause.php

function envv(string $k, $default = null) {
    $v = getenv($k);
    return ($v === false || $v === '') ? $default : $v;
}

function getAccessToken(): string {
    // Prefer a static ACCESS_TOKEN if you really want to manage it yourself.
    $token = envv('ACCESS_TOKEN');
    if ($token) return $token;

    $clientId = envv('CLIENT_ID');
    $clientSecret = envv('CLIENT_SECRET');
    if (!$clientId || !$clientSecret) {
        fwrite(STDERR, "[ERR] Missing CLIENT_ID/CLIENT_SECRET and no ACCESS_TOKEN set.\n");
        exit(1);
    }

    $ch = curl_init('https://api.revcontent.io/oauth/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'advertiser publisher'
        ]),
    ]);

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($code !== 200) {
        fwrite(STDERR, "[ERR] Token request failed (HTTP $code): $resp $err\n");
        exit(1);
    }
    $data = json_decode($resp, true);
    $token = $data['access_token'] ?? '';
    if (!$token) {
        fwrite(STDERR, "[ERR] Token missing in response: $resp\n");
        exit(1);
    }
    return $token;
}

function postBoost(string $token, int $campaignId, string $enabled): array {
    $ch = curl_init('https://api.revcontent.io/stats/api/v1.0/boosts');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
        ],
        CURLOPT_POSTFIELDS => json_encode([
            "id" => $campaignId,
            "enabled" => $enabled
        ]),
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return [$code, $resp, $err];
}

// Determine local NY time (DST-safe)
$ny = new DateTimeZone('America/New_York');
$now = new DateTime('now', $ny);
$hour = (int)$now->format('G'); // 0..23 local
$enabled = ($hour >= 9 && $hour < 24) ? 'on' : 'off';

// Campaign list
$idsEnv = envv('CAMPAIGN_IDS', '2453224,2448613');
$campaignIds = array_values(array_filter(array_map('trim', explode(',', $idsEnv)), 'strlen'));

$token = getAccessToken();

echo "[" . $now->format('Y-m-d H:i:s T') . "] Window=" . ($enabled === 'on' ? 'ON' : 'OFF') . " (hour=$hour)\n";

foreach ($campaignIds as $idStr) {
    $id = (int)$idStr;
    [$code, $resp, $err] = postBoost($token, $id, $enabled);
    echo "Campaign $id → " . strtoupper($enabled) . " → HTTP $code\n";
    if ($err)   echo "cURL error: $err\n";
    if ($resp)  echo "Response: $resp\n";
    echo "\n";
}
