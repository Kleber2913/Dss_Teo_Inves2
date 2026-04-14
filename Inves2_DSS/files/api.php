<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$metodo = $_SERVER['REQUEST_METHOD'];
$conn   = conectarDB();

if ($metodo === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $stmt = $conn->prepare('SELECT id, nombre, precio, cantidad, creado_en FROM productos WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['error' => true, 'mensaje' => 'Producto no encontrado']);
        } else {
            echo json_encode(['error' => false, 'data' => $res->fetch_assoc()]);
        }
        $stmt->close();
    } else {
        $res = $conn->query('SELECT id, nombre, precio, cantidad, creado_en FROM productos ORDER BY id DESC');
        $data = [];
        while ($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode(['error' => false, 'total' => count($data), 'data' => $data]);
    }

} elseif ($metodo === 'POST') {
    $body     = json_decode(file_get_contents('php://input'), true);
    $nombre   = trim($body['nombre']   ?? '');
    $precio   = $body['precio']   ?? null;
    $cantidad = $body['cantidad'] ?? null;

    if ($nombre === '' || $precio === null || $cantidad === null || !is_numeric($precio) || !is_numeric($cantidad)) {
        http_response_code(400);
        echo json_encode(['error' => true, 'mensaje' => 'Datos inválidos']);
        $conn->close(); exit;
    }

    $stmt = $conn->prepare('INSERT INTO productos (nombre, precio, cantidad) VALUES (?, ?, ?)');
    $stmt->bind_param('sdi', $nombre, $precio, $cantidad);
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['error' => false, 'mensaje' => 'Producto creado', 'data' => ['id' => $conn->insert_id, 'nombre' => $nombre, 'precio' => (float)$precio, 'cantidad' => (int)$cantidad]]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => true, 'mensaje' => $stmt->error]);
    }
    $stmt->close();

} elseif ($metodo === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); echo json_encode(['error' => true, 'mensaje' => 'ID inválido']); $conn->close(); exit; }

    $stmt = $conn->prepare('DELETE FROM productos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => true, 'mensaje' => 'Producto no encontrado']);
    } else {
        echo json_encode(['error' => false, 'mensaje' => 'Producto eliminado']);
    }
    $stmt->close();

} else {
    http_response_code(405);
    echo json_encode(['error' => true, 'mensaje' => 'Método no permitido']);
}

$conn->close();
