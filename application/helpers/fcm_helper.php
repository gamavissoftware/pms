<?php

function getAccessToken()
{

    $json = json_decode(file_get_contents(APPPATH . 'config/firebase.json'), true);

    $jwtHeader = base64_encode(json_encode([
        "alg" => "RS256",
        "typ" => "JWT"
    ]));

    $now = time();

    $jwtClaim = base64_encode(json_encode([
        "iss" => $json['client_email'],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => $json['token_uri'],
        "exp" => $now + 3600,
        "iat" => $now
    ]));

    openssl_sign("$jwtHeader.$jwtClaim", $signature, $json['private_key'], 'SHA256');
    $jwtSignature = base64_encode($signature);

    $jwt = "$jwtHeader.$jwtClaim.$jwtSignature";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $json['token_uri']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    return $response['access_token'];
}


function sendFCM($token, $title, $message, $dataPayload = [])
{
    $accessToken = getAccessToken();

    $projectId = "shubhampackapp";

    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

   $body = [
    "message" => [
        "token" => $token,
        "notification" => [
            "title" => $title,
            "body" => $message
        ],
        "android" => [
            "priority" => "high",
            "notification" => [
                "sound" => "default",
                "channel_id" => "high_importance_channel"
            ]
        ],
        "data" => $dataPayload
    ]
];

log_message('error', '🔥 FCM PAYLOAD: ' . json_encode($body));
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);



    if ($response === false) {
        die("CURL ERROR (FCM): " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}


function sendFCMData($token, $title, $message, $dataPayload = [])
{
    $accessToken = getAccessToken();
    $projectId = "shubhampackapp";

    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    /// 🔥 ADD TITLE + MESSAGE INTO DATA ALSO
    $dataPayload['title'] = $title;
    $dataPayload['message'] = $message;

    $body = [
        "message" => [
            "token" => $token,

            /// 🔥 ANDROID CONFIG (IMPORTANT)
            "android" => [
                "priority" => "high",
                "ttl" => "30s",
                "notification" => [
                    "channel_id" => "high_importance_channel",
                    "sound" => "default"
                ]
            ],

            /// 🔥 THIS ENSURES DELIVERY ALWAYS
            "notification" => [
                "title" => $title,
                "body" => $message
            ],

            /// 🔥 YOUR CUSTOM DATA (USED IN APP)
            "data" => $dataPayload
        ]
    ];

    /// 🔥 DEBUG
    log_message('error', '🔥 FCM PAYLOAD: ' . json_encode($body));

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if ($response === false) {
        log_message('error', '❌ FCM CURL ERROR: ' . curl_error($ch));
    } else {
        log_message('error', '🔥 FCM RESPONSE: ' . $response);
    }

    curl_close($ch);

    return $response;
}