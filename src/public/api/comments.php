<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/../data/comments.json';

function readData($f) {
    return file_exists($f) ? json_decode(file_get_contents($f), true) : ['comentarios' => []];
}
function writeData($f, $d) {
    file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: listar comentarios de un producto ──
if ($method === 'GET') {
    $pid = $_GET['producto_id'] ?? '';
    $data = readData($file);
    $list = array_values(array_filter($data['comentarios'], fn($c) => $c['producto_id'] === $pid));
    usort($list, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));
    $usuario = $_SESSION['usuari'] ?? null;
    foreach ($list as &$c) {
        $c['es_mio']  = $usuario && $c['usuario'] === $usuario;
        $c['liked']   = $usuario && in_array($usuario, $c['liked_by'] ?? []);
    }
    echo json_encode(array_values($list));
    exit;
}

// ── POST: add / like / delete ──
if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';
    $usuario = $_SESSION['usuari'] ?? null;

    if (!$usuario) {
        http_response_code(401);
        echo json_encode(['error' => 'Debes iniciar sesión.']);
        exit;
    }

    $data = readData($file);

    // Añadir comentario
    if ($action === 'add') {
        $texto     = trim($body['texto'] ?? '');
        $valoracion = (int)($body['valoracion'] ?? 0);
        $pid       = $body['producto_id'] ?? '';

        if (!$texto || $valoracion < 1 || $valoracion > 5 || !$pid) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos.']);
            exit;
        }

        $ids   = array_column($data['comentarios'], 'id');
        $newId = $ids ? max($ids) + 1 : 1;

        $c = [
            'id'          => $newId,
            'producto_id' => $pid,
            'usuario'     => $usuario,
            'texto'       => htmlspecialchars($texto, ENT_QUOTES, 'UTF-8'),
            'valoracion'  => $valoracion,
            'likes'       => 0,
            'liked_by'    => [],
            'fecha'       => gmdate('Y-m-d\TH:i:s\Z'),
        ];

        $data['comentarios'][] = $c;
        writeData($file, $data);

        $c['es_mio'] = true;
        $c['liked']  = false;
        echo json_encode($c);
        exit;
    }

    // Like / unlike
    if ($action === 'like') {
        $id = (int)($body['id'] ?? 0);
        foreach ($data['comentarios'] as &$c) {
            if ($c['id'] === $id) {
                $lb = $c['liked_by'] ?? [];
                if (in_array($usuario, $lb)) {
                    $c['liked_by'] = array_values(array_filter($lb, fn($u) => $u !== $usuario));
                    $c['likes']    = max(0, $c['likes'] - 1);
                    $liked = false;
                } else {
                    $c['liked_by'][] = $usuario;
                    $c['likes']++;
                    $liked = true;
                }
                writeData($file, $data);
                echo json_encode(['likes' => $c['likes'], 'liked' => $liked]);
                exit;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'No encontrado.']);
        exit;
    }

    // Eliminar
    if ($action === 'delete') {
        $id = (int)($body['id'] ?? 0);
        $found = false;
        foreach ($data['comentarios'] as $c) {
            if ($c['id'] === $id) {
                $found = true;
                if ($c['usuario'] !== $usuario) {
                    http_response_code(403);
                    echo json_encode(['error' => 'Sin permiso.']);
                    exit;
                }
                break;
            }
        }
        if (!$found) { http_response_code(404); echo json_encode(['error' => 'No encontrado.']); exit; }

        $data['comentarios'] = array_values(array_filter($data['comentarios'], fn($c) => $c['id'] !== $id));
        writeData($file, $data);
        echo json_encode(['ok' => true]);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Acción no válida.']);
