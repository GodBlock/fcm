<?php

$url = 'https://fcm.googleapis.com/v1/projects/matrixrapidxd/messages:send';

$headers = [
    'Authorization: Bearer ' . getAccessToken(),
    'Content-Type: application/json'
];

$message = [
    'message' => [
        'token' => 'eJb6fgM3R3ahqGRmWQ9-Qd:APA91bEIR1z7aSthIsFd8S8dRN4INthhqQofkSi-ZbWc6lE__vvP6DG99E_ZZP0tNKdGPcg_Uk2DA8cyxZsAURt38pPgvdAo0Dkm7T6ecG9L-jMxwWV_HFc',
        'notification' => [
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación ☘️',
                ],
        'data' => [
            'key' => 'value',
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRFJgC_cWRlZm_KuPMqvyCwKBlBWc1BdpAcvQ&s'
    
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

$response = curl_exec($ch);
curl_close($ch);

echo $response;

function getAccessToken() {
    $serviceAccountPath = 'credenciales/jsoncred.json';
    $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

    $url = 'https://oauth2.googleapis.com/token';
    $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => createJWT($serviceAccount)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData['access_token'];
}

function createJWT($serviceAccount) {
    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $claims = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => time() + 3600,
        'iat' => time()
    ];

    $payload = json_encode($claims);

    $headerBase64 = base64_encode($header);
    $payloadBase64 = base64_encode($payload);

    $signature = '';
    openssl_sign("$headerBase64.$payloadBase64", $signature, $serviceAccount['private_key'], 'sha256');
    $signatureBase64 = base64_encode($signature);

    return "$headerBase64.$payloadBase64.$signatureBase64";
}