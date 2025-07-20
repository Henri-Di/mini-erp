<?php
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../controllers/CupomController.php';

// Configuração PDO - ajuste as credenciais conforme seu ambiente
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=mini_erp;charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

$controller = new CupomController($conn);

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

switch ($method) {
    case 'GET':
        if (isset($_GET['codigo'])) {
            $codigo = $_GET['codigo'];
            $cupom = $controller->buscarPorCodigo($codigo);
            if ($cupom) {
                echo json_encode([
                    'id' => $cupom->getId(),
                    'codigo' => $cupom->getCodigo(),
                    'desconto' => $cupom->getDesconto(),
                    'minimo_subtotal' => $cupom->getMinimoSubtotal(),
                    'validade' => $cupom->getValidade()
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Cupom não encontrado']);
            }
        } else {
            // listar todos
            $cupons = $controller->listarTodos();
            $data = array_map(function ($c) {
                return [
                    'id' => $c->getId(),
                    'codigo' => $c->getCodigo(),
                    'desconto' => $c->getDesconto(),
                    'minimo_subtotal' => $c->getMinimoSubtotal(),
                    'validade' => $c->getValidade()
                ];
            }, $cupons);
            echo json_encode($data);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !is_array($data) ||
            !isset($data['codigo'], $data['desconto'], $data['minimo_subtotal'], $data['validade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        $cupom = new Cupom(
            $data['codigo'],
            (float)$data['desconto'],
            (float)$data['minimo_subtotal'],
            $data['validade']
        );

        if ($controller->salvar($cupom)) {
            http_response_code(201);
            echo json_encode(['message' => 'Cupom criado', 'id' => $cupom->getId()]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar cupom']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            !is_array($data) ||
            !isset($data['id'], $data['codigo'], $data['desconto'], $data['minimo_subtotal'], $data['validade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        $cupom = new Cupom(
            $data['codigo'],
            (float)$data['desconto'],
            (float)$data['minimo_subtotal'],
            $data['validade'],
            (int)$data['id']
        );

        if ($controller->salvar($cupom)) {
            echo json_encode(['message' => 'Cupom atualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar cupom']);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $input);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório']);
            exit;
        }
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Cupom deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar cupom']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
