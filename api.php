<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tienda');

// Configuración de CORS (permite peticiones desde cualquier origen)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Conexión a la base de datos
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

// Obtener parámetros de consulta
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;

// Conectar a la base de datos
$db = conectarDB();

// Construir la consulta SQL
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

// Preparar y ejecutar la consulta
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

// Obtener resultados
$resultado = $stmt->get_result();
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

// Cerrar conexión
$db->close();

// Devolver respuesta
echo json_encode([
    'estado' => 'éxito',
    'total' => count($productos),
    'productos' => $productos
]);
?>