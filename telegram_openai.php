<?php
//show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// token telegram
$token= "token_telegram";
$api_telegram="https://api.telegram.org/bot".$token;

$input = file_get_contents("php://input");
$content= json_decode($input, TRUE);

$chatID = $content["message"]["chat"]["id"];
$message = $content["message"]["text"];

// Tu clave de API de OpenAI
$api_key = 'openai_api_key';

// URL de la API de OpenAI GPT-3
$url = 'https://api.openai.com/v1/chat/completions';

// Datos de entrada para GPT-3
$data = [
    'model' => "gpt-3.5-turbo",
    'messages' => [
        [
            'role' => 'system',
            'content' => 'system'
        ],
        [
            'role' => 'user',
            'content' => $message
        ]
    ]
];

// Configuración de la solicitud HTTP
$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ],
];

// Inicializar cURL
$ch = curl_init();

// Configurar opciones de cURL
curl_setopt_array($ch, $options);

// Realizar la solicitud a la API
$response = curl_exec($ch);

// Manejar errores de cURL
if (curl_errno($ch)) {
   file_get_contents($api_telegram."/sendmessage?chat_id=".$chatID."&text=".curl_error($ch));
    echo 'Error:' . curl_error($ch);
} else {
    // Decodificar la respuesta JSON
    $response_data = json_decode($response, true);

    // Obtener la respuesta de GPT-3
    $output = $response_data['choices'][0]['message']['content'];

    // Imprimir la respuesta
    file_get_contents($api_telegram."/sendmessage?chat_id=".$chatID."&text=".$output);
   
}

// Cerrar la conexión cURL
curl_close($ch);






?>