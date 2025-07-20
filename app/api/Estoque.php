<?php
// Inclusão dos arquivos essenciais: configuração, conexão com banco, modelo Estoque e controller
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../controllers/EstoqueController.php';

// Configurações de cabeçalhos HTTP para permitir CORS e definir o tipo de conteúdo JSON
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem (para desenvolvimento)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Cabeçalhos permitidos
header('Content-Type: application/json; charset=utf-8'); // Resposta JSON com charset UTF-8

// Responde imediatamente para requisições OPTIONS (preflight do CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Tenta criar instância da classe Database para conexão ao banco
try {
    $db = new Database();
} catch (Exception $e) {
    // Em caso de falha na conexão, retorna erro 500 e mensagem JSON
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Cria o controller responsável por manipular o estoque
$controller = new EstoqueController($db);

// Captura o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Switch para tratar os diferentes métodos HTTP e executar ações correspondentes
switch ($method) {

    case 'GET':
        // Se for passado produto_id na query, lista todos os estoques para esse produto
        if (isset($_GET['produto_id'])) {
            $produtoId = (int)$_GET['produto_id'];

            // Chama método que retorna array de Estoque para o produto
            $estoques = $controller->listarPorProdutoId($produtoId);

            // Converte objetos Estoque em arrays simples para JSON
            $data = array_map(fn($e) => [
                'id' => $e->getId(),
                'produto_id' => $e->getProdutoId(),
                'variacao' => $e->getVariacao(),
                'quantidade' => $e->getQuantidade()
            ], $estoques);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);

        // Se for passado id na query, busca estoque específico pelo ID
        } elseif (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $estoque = $controller->buscarPorId($id);

            if ($estoque) {
                // Retorna dados do estoque encontrado em JSON
                echo json_encode([
                    'id' => $estoque->getId(),
                    'produto_id' => $estoque->getProdutoId(),
                    'variacao' => $estoque->getVariacao(),
                    'quantidade' => $estoque->getQuantidade()
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // Estoque não encontrado - retorna 404
                http_response_code(404);
                echo json_encode(['error' => 'Estoque não encontrado'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Parâmetro obrigatório não informado na query string
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro produto_id ou id obrigatório'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'POST':
        // Lê dados JSON enviados no corpo para criação de novo estoque
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação simples dos campos obrigatórios
        if (!is_array($data) || !isset($data['produto_id'], $data['variacao'], $data['quantidade'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Cria objeto Estoque com os dados recebidos
        $estoque = new Estoque($data['produto_id'], $data['variacao'], $data['quantidade']);

        // Tenta salvar o estoque no banco e retorna resposta conforme sucesso/falha
        if ($controller->salvar($estoque)) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Estoque criado', 'id' => $estoque->getId()], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar estoque'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'PUT':
        // Lê dados JSON para atualizar estoque existente
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação dos campos obrigatórios, incluindo o ID do estoque
        if (!is_array($data) || !isset($data['id'], $data['produto_id'], $data['variacao'], $data['quantidade'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Cria objeto Estoque com ID para atualização
        $estoque = new Estoque($data['produto_id'], $data['variacao'], $data['quantidade'], $data['id']);

        // Tenta salvar (atualizar) o estoque e retorna resposta
        if ($controller->salvar($estoque)) {
            echo json_encode(['message' => 'Estoque atualizado'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar estoque'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'DELETE':
        // Para DELETE, lê dados enviados no corpo (php://input) e converte para array
        parse_str(file_get_contents('php://input'), $input);

        // Valida presença do ID para exclusão
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Tenta deletar o estoque pelo ID e responde conforme resultado
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Estoque deletado'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar estoque'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    default:
        // Se método HTTP não é suportado, retorna erro 405 (Method Not Allowed)
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
        exit;
}
