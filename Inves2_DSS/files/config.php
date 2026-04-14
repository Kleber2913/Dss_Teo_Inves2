<?php
// ============================================
//  config.php  –  Configuración de BD
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Cambia por tu usuario
define('DB_PASS', '');           // Cambia por tu contraseña
define('DB_NAME', 'api_productos');

function conectarDB(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode([
            'error'   => true,
            'mensaje' => 'Error de conexión: ' . $conn->connect_error
        ]);
        exit;
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
