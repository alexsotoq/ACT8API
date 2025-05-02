<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tienda');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conexion->connect_error) {
        http_response_code(500);
        die(json_encode([
            'estado' => 'error',
            'mensaje' => 'Error de conexión: ' . $conexion->connect_error
        ]));
    }

    $conexion->set_charset("utf8");
    return $conexion;
}

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;

$db = conectarDB();

$sql = "SELECT * FROM productos";
$condiciones = [];
$parametros = [];
$tipos = "";

if ($categoria) {
    $condiciones[] = "categoria = ?";
    $parametros[] = $categoria;
    $tipos .= "s";
}

if ($busqueda) {
    $condiciones[] = "(nombre LIKE ? OR marca LIKE ?)";
    $parametros[] = "%$busqueda%";
    $parametros[] = "%$busqueda%";
    $tipos .= "ss";
}

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$stmt = $db->prepare($sql);

if ($parametros) {
    $stmt->bind_param($tipos, ...$parametros);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'Error al ejecutar la consulta: ' . $stmt->error
    ]);
    exit;
}

$resultado = $stmt->get_result();
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

$db->close();

echo json_encode([
    'estado' => 'éxito',
    'total' => count($productos),
    'productos' => $productos
]);
?>