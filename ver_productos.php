<?php
// 1. Configuración inicial
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8'); // IMPORTANTE

// 2. Obtener datos de la API local
$api_file = __DIR__ . '/api.php';

if (!file_exists($api_file)) {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'No se encontró api.php en la misma carpeta'
    ]);
    exit;
}

// 3. Capturar la salida de la API
ob_start();
include $api_file;
$api_response = ob_get_clean();

// 4. Validar y retornar el JSON
$data = json_decode($api_response, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'La respuesta de la API no es JSON válido',
        'original' => $api_response
    ]);
}
