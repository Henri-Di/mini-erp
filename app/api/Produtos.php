<?php
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../models/Produto.php';
require_once __DIR__.'/../controllers/ProdutoController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=mini_erp;charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

$controller = new ProdutoController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
// Assumindo URL tipo /api/produtos.php ou /api/produtos.php?id=1

$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            $produto = $controller->buscarPorId((int)$id);
            if ($produto) {
                echo json_encode([
                    'id' => $produto->getId(),
                    'nome' => $produto->getNome(),
                    'preco' => $produto->getPreco(),
                    'criado_em' => $produto->getCriadoEm()
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Produto não encontrado']);
            }
        } else {
            $produtos = $controller->listarTodos();
            $data = [];
            foreach ($produtos as $p) {
                $data[] = [
                    'id' => $p->getId(),
                    'nome' => $p->getNome(),
                    'preco' => $p->getPreco(),
                    'criado_em' => $p->getCriadoEm()
                ];
            }
            echo json_encode($data);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['nome'], $input['preco'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit;
        }
        $produto = new Produto($input['nome'], (float)$input['preco']);
        $success = $controller->salvar($produto);
        if ($success) {
            http_response_code(201);
            echo json_encode(['message' => 'Produto criado', 'id' => $produto->getId()]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao salvar produto']);
        }
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID necessário para atualizar']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['nome'], $input['preco'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit;
        }
        $produto = $controller->buscarPorId((int)$id);
        if (!$produto) {
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado']);
            exit;
        }
        $produto->setNome($input['nome']);
        $produto->setPreco((float)$input['preco']);
        $success = $controller->salvar($produto);
        if ($success) {
            echo json_encode(['message' => 'Produto atualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao atualizar produto']);
        }
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID necessário para deletar']);
            exit;
        }
        $success = $controller->deletar((int)$id);
        if ($success) {
            echo json_encode(['message' => 'Produto deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao deletar produto']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
}
