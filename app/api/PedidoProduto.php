<?php
require_once __DIR__ . '/../models/PedidoProduto.php';
require_once __DIR__ . '/../controllers/PedidoProdutoController.php';

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
$controller = new PedidoProdutoController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $item = $controller->buscarPorId((int)$_GET['id']);
            if ($item) {
                echo json_encode($item->toArray());
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Item não encontrado']);
            }
        } elseif (isset($_GET['pedido_id'])) {
            $lista = $controller->listarPorPedidoId((int)$_GET['pedido_id']);
            $data = array_map(fn($p) => $p->toArray(), $lista);
            echo json_encode($data);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro id ou pedido_id obrigatório']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !isset($data['pedidoId'], $data['produtoId'], $data['variacao'], $data['quantidade'], $data['precoUnitario'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }
        try {
            $item = new PedidoProduto(
                (int)$data['pedidoId'],
                (int)$data['produtoId'],
                $data['variacao'],
                (int)$data['quantidade'],
                (float)$data['precoUnitario']
            );
            if ($controller->salvar($item)) {
                http_response_code(201);
                echo json_encode(['message' => 'Item criado', 'id' => $item->getId()]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao salvar item']);
            }
        } catch (InvalidArgumentException $ex) {
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !isset($data['id'], $data['pedidoId'], $data['produtoId'], $data['variacao'], $data['quantidade'], $data['precoUnitario'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }
        try {
            $item = new PedidoProduto(
                (int)$data['pedidoId'],
                (int)$data['produtoId'],
                $data['variacao'],
                (int)$data['quantidade'],
                (float)$data['precoUnitario'],
                (int)$data['id']
            );
            if ($controller->salvar($item)) {
                echo json_encode(['message' => 'Item atualizado']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar item']);
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
            echo json_encode(['message' => 'Item deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar item']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
