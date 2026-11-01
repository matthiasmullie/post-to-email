<?php

/**
 * Validate a Turnstile response token with Cloudflare's Siteverify API.
 */
function is_valid_turnstile_token(string $token, string $secretKey, ?string $remoteIp = null): bool {
    if ($token === '' || strlen($token) > 2048) {
        return false;
    }

    $data = [
        'secret' => $secretKey,
        'response' => $token,
    ];
    if ($remoteIp !== null && $remoteIp !== '') {
        $data['remoteip'] = $remoteIp;
    }

    $curl = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    if ($curl === false) {
        return false;
    }

    if (!curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
    ])) {
        curl_close($curl);
        return false;
    }

    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($response === false || $statusCode !== 200) {
        return false;
    }

    $validation = json_decode($response, true);

    return is_array($validation) && ($validation['success'] ?? false) === true;
}
