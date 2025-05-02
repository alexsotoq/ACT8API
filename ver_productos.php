<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'tienda';

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $mysqli->connect_error]);
    exit;
}

$sql = "SELECT * FROM productos";
$resultado = $mysqli->query($sql);

$productos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
}

$mysqli->close();

echo json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);