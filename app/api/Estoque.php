<?php
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../controllers/EstoqueController.php';

// Configurar conexão PDO - ajuste os dados conforme seu ambiente
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=mini_erp;charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

$controller = new EstoqueController($conn);

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

switch ($method) {
    case 'GET':
        // listar variações/estoques por produto_id
        if (isset($_GET['produto_id'])) {
            $produtoId = (int) $_GET['produto_id'];
            $estoques = $controller->listarPorProdutoId($produtoId);
            $data = array_map(function ($e) {
                return [
                    'id' => $e->getId(),
                    'produto_id' => $e->getProdutoId(),
                    'variacao' => $e->getVariacao(),
                    'quantidade' => $e->getQuantidade()
                ];
            }, $estoques);
            echo json_encode($data);
        } elseif (isset($_GET['id'])) {
            // buscar estoque por id
            $id = (int) $_GET['id'];
            $estoque = $controller->buscarPorId($id);
            if ($estoque) {
                echo json_encode([
                    'id' => $estoque->getId(),
                    'produto_id' => $estoque->getProdutoId(),
                    'variacao' => $estoque->getVariacao(),
                    'quantidade' => $estoque->getQuantidade()
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Estoque não encontrado']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro produto_id ou id obrigatório']);
        }
        break;

    case 'POST':
        // criar nova variação/estoque
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !is_array($data) ||
            !isset($data['produto_id'], $data['variacao'], $data['quantidade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        $estoque = new Estoque($data['produto_id'], $data['variacao'], $data['quantidade']);
        if ($controller->salvar($estoque)) {
            http_response_code(201);
            echo json_encode(['message' => 'Estoque criado', 'id' => $estoque->getId()]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar estoque']);
        }
        break;

    case 'PUT':
        // atualizar variação/estoque
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !is_array($data) ||
            !isset($data['id'], $data['produto_id'], $data['variacao'], $data['quantidade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        $estoque = new Estoque($data['produto_id'], $data['variacao'], $data['quantidade'], $data['id']);
        if ($controller->salvar($estoque)) {
            echo json_encode(['message' => 'Estoque atualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar estoque']);
        }
        break;

    case 'DELETE':
        // deletar estoque
        parse_str(file_get_contents('php://input'), $input);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório']);
            exit;
        }
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Estoque deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar estoque']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
