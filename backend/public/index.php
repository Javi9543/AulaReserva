<?php
require_once __DIR__ . '/../src/bootstrap.php';
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

$metodo = $_SERVER['REQUEST_METHOD'];
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($metodo === 'GET' && $url === '/api/health') {
    echo json_encode(["ok" => true]);
    exit;
}

if ($metodo === 'GET' && $url === '/api/aulas') {
    $consulta = $db->query("SELECT id, nombre FROM aulas;");
    $aulas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["aulas" => $aulas]);
    exit;
}

if ($metodo === 'POST' && $url === '/api/reservas') {
    $input = json_decode(file_get_contents('php://input'), true);
    $aulaId = $input['aula_id'] ?? $input['aula'] ?? null;
    $fecha = $input['fecha'] ?? null;
    $hora = $input['hora'] ?? null;
    $responsable = $input['responsable'] ?? null;

    if (empty($aulaId) || empty($fecha) || empty($hora) || empty($responsable)) {
        http_response_code(400);
        echo json_encode(['error' => 'No has completado todos los campos']);
        exit;
    }

    $comprobacion = $db->prepare("SELECT id FROM reservas WHERE aula_id = ? AND fecha = ? AND hora = ?");
    $comprobacion->execute([$aulaId, $fecha, $hora]);
    if ($comprobacion->fetch()) {
        http_response_code(409);
        echo json_encode(["error" => "Ya existe una reserva para ese aula, fecha y hora"]);
        exit;
    }

    $consulta = $db->prepare("INSERT INTO reservas (aula_id, fecha, hora, responsable) VALUES (?, ?, ?, ?)");
    $consulta->execute([$aulaId, $fecha, $hora, $responsable]);

    http_response_code(201);
    echo json_encode([
        'id' => (int) $db->lastInsertId(),
        'message' => 'Reserva de aula recibida correctamente'
    ]);
    exit;
}

if ($metodo === 'GET' && $url === '/api/reservas') {
    $consulta = $db->query("SELECT id, aula_id, fecha, hora, responsable FROM reservas;");
    $reservas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["reservas" => $reservas]);
    exit;
}

if ($metodo === 'DELETE' && str_starts_with($url, "/api/reservas/")) {
    $partes = explode("/", trim($url, "/"));
    $id = $partes[2] ?? null;

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(["error" => "ID no válido"]);
        exit;
    }

    $consulta = $db->prepare("DELETE FROM reservas WHERE id = ?");
    $consulta->execute([$id]);

    http_response_code(200);
    echo json_encode(["mensaje" => "Reserva cancelada"]);
    exit;
}