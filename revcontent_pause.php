<?php
declare(strict_types=1);

/**
 * Revcontent time-based campaign toggler for Render Cron
 *
 * ENV VARS (Render -> Environment):
 *   ACCESS_TOKEN   = <your bearer token>  (required)
 *   CAMPAIGN_IDS   = 2453224,2448613      (optional, comma-separated)
 *
 * CRON (Render):
 *   0 * * * *      # run hourly
 *
 * Logic:
 *   ON between 9:00 AM and 11:59 PM EST (UTC 14:00–04:59).
 *   OFF otherwise.
 *
 * NOTE: This uses fixed EST (UTC-5). If you actually want local US Eastern
 * with daylight saving (EDT), update the window accordingly.
 */

const API_URL = 'https://api.revcontent.io/stats/api/v1.0/boosts';

// --- Env & input ---
$accessToken = getenv('ACCESS_TOKEN');
if (!$accessToken) {
    fwrite(STDERR, "[" . gmdate('Y-m-d H:i:s') . " UTC] ERROR: ACCESS_TOKEN is not set.\n");
    exit(1);
}

$idsEnv = getenv('CAMPAIGN_IDS');
if ($idsEnv) {
    $campaignIds = array_values(array_filter(array_map('intval', explode(',', $idsEnv))));
} else {
    // Fallback to your known campaigns
    $campaignIds = [2453224, 2448613];
}
if (empty($campaignIds)) {
    fwrite(STDERR, "[" . gmdate('Y-m-d H:i:s') . " UTC] ERROR: No campaign IDs provided.\n");
    exit(1);
}

// --- Time window: 9 AM–11:59 PM EST => 14:00–04:59 UTC ---
$utcHour = (int) gmdate('G'); // 0–23 UTC hour
$enabled  = ($utcHour >= 14 || $utcHour < 5) ? 'on' : 'off';

// --- Helper: call API for one campaign ---
function toggleCampaign(int $campaignId, string $enabled, string $token): array {
    $ch = curl_init(API_URL);
    $payload = json_encode([
        'id'      => $campaignId,
        'enabled' => $enabled,
    ]);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $body     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    return [
        'httpCode' => $httpCode ?: 0,
        'body'     => $body === false ? '' : $body,
        'error'    => $err,
    ];
}

// --- Run for each campaign ---
$now = gmdate('Y-m-d H:i:s');
foreach ($campaignIds as $id) {
    $res = toggleCampaign($id, $enabled, $accessToken);

    echo "[{$now} UTC] Campaign {$id} → " . strtoupper($enabled) .
         " → HTTP {$res['httpCode']}\n";

    if ($res['error']) {
        echo "cURL error: {$res['error']}\n\n";
    } else {
        echo "Response: {$res['body']}\n\n";
    }
}
