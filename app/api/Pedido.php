<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../controllers/PedidoController.php';

// Configurar conexão PDO (ajuste conforme seu ambiente)
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=mini_erp;charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
$controller = new PedidoController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $pedido = $controller->buscarPorId((int)$_GET['id']);
            if ($pedido) {
                echo json_encode($pedido->toArray());
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Pedido não encontrado']);
            }
        } else {
            $pedidos = $controller->listarTodos();
            $data = array_map(fn($p) => $p->toArray(), $pedidos);
            echo json_encode($data);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !isset($data['valorTotal'], $data['frete'], $data['endereco'], $data['cep'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }
        try {
            $pedido = new Pedido(
                (float)$data['valorTotal'],
                (float)$data['frete'],
                $data['endereco'],
                $data['cep'],
                $data['status'] ?? 'pendente'
            );
            if ($controller->salvar($pedido)) {
                http_response_code(201);
                echo json_encode(['message' => 'Pedido criado', 'id' => $pedido->getId()]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao salvar pedido']);
            }
        } catch (InvalidArgumentException $ex) {
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !isset($data['id'], $data['valorTotal'], $data['frete'], $data['endereco'], $data['cep'], $data['status'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }
        try {
            $pedido = new Pedido(
                (float)$data['valorTotal'],
                (float)$data['frete'],
                $data['endereco'],
                $data['cep'],
                $data['status'],
                (int)$data['id']
            );
            if ($controller->salvar($pedido)) {
                echo json_encode(['message' => 'Pedido atualizado']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar pedido']);
            }
        } catch (InvalidArgumentException $ex) {
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()]);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $input);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório para deletar']);
            exit;
        }
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Pedido deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar pedido']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
