<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

$tokenUrl = 'https://auth.foxdata.app/connect/token';

// Los datos del cuerpo (Body)
$body = [
    'grant_type'    => 'client_credentials',
    'client_id'     => 'smvt-apiweb-C0006',
    'client_secret' => 'i84so7BEzsUo',
    'scope'         => 'smartventas-api smartventas.service.read smartventas.service.write'
];

$ch = curl_init($tokenUrl);

// Configuramos cURL para que se comporte como el Invoke-RestMethod
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

// IMPORTANTE: http_build_query genera el formato x-www-form-urlencoded automáticamente
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);

// Añadimos User-Agent para evitar bloqueos de seguridad (403)
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Cursor/Agent');

// Solo usa estos si el servidor tiene certificados auto-firmados (en producción es mejor quitarlos)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
} else {
    // Si devuelve 403, imprimimos la respuesta para ver el motivo exacto (invalid_scope, etc.)
    if ($httpCode !== 200) {
        echo json_encode([
            "http_code" => $httpCode,
            "server_response" => json_decode($response) ?: $response
        ]);
    } else {
        echo $response;
    }
}

curl_close($ch);
?>