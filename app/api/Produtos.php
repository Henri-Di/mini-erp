<?php
// Inclusão dos arquivos essenciais: configuração, conexão com banco, modelo Produto e controller
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../core/Database.php';
require_once __DIR__.'/../models/Produto.php';
require_once __DIR__.'/../controllers/ProdutoController.php';

// Configurações dos cabeçalhos HTTP para permitir CORS e definir o tipo de conteúdo JSON UTF-8
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem (útil para desenvolvimento)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Cabeçalhos permitidos na requisição
header('Content-Type: application/json; charset=utf-8'); // Define que o conteúdo da resposta será JSON UTF-8

// Responde diretamente para requisições OPTIONS (pré voo do CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Tenta criar conexão com o banco de dados via classe Database
try {
    $db = new Database();
} catch (Exception $e) {
    // Em caso de erro na conexão, retorna código 500 e mensagem JSON
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Instancia o controller responsável pelas operações de Produto
$controller = new ProdutoController($db);

// Obtém o método HTTP da requisição (GET, POST, PUT, DELETE, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Obtém o parâmetro 'id' da query string, caso exista (para buscar/atualizar/deletar produto específico)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Switch para tratar cada método HTTP de forma apropriada
switch ($method) {

    case 'GET':
        if ($id) {
            // Busca produto específico pelo ID
            $produto = $controller->buscarPorId($id);
            if ($produto) {
                // Retorna o produto encontrado como array JSON
                echo json_encode($produto->toArray(), JSON_UNESCAPED_UNICODE);
            } else {
                // Produto não encontrado: retorna status 404
                http_response_code(404);
                echo json_encode(['error' => 'Produto não encontrado'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Sem ID, lista todos os produtos cadastrados
            $produtos = $controller->listarTodos();

            // Converte array de objetos Produto para arrays simples para JSON
            $data = array_map(fn($p) => $p->toArray(), $produtos);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'POST':
        // Lê dados JSON enviados no corpo da requisição para criação de produto
        $input = json_decode(file_get_contents('php://input'), true);

        // Valida se os dados obrigatórios foram enviados
        if (!$input || !isset($input['nome'], $input['preco'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Cria um novo objeto Produto com os dados fornecidos
            $produto = new Produto($input['nome'], (float)$input['preco']);

            // Tenta salvar o produto no banco via controller
            $success = $controller->salvar($produto);

            if ($success) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Produto criado', 'id' => $produto->getId()], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Falha ao salvar produto'], JSON_UNESCAPED_UNICODE);
            }
        } catch (InvalidArgumentException $ex) {
            // Captura exceções de validação e retorna erro 400 com mensagem
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'PUT':
        // Para atualizar, é obrigatório informar o ID via query string
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID necessário para atualizar'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Lê dados JSON para atualização do produto
        $input = json_decode(file_get_contents('php://input'), true);

        // Valida se os dados obrigatórios para atualização foram enviados
        if (!$input || !isset($input['nome'], $input['preco'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Busca o produto existente para alterar
        $produto = $controller->buscarPorId($id);

        if (!$produto) {
            // Produto não encontrado: retorna 404
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Atualiza os dados do produto com os valores recebidos
            $produto->setNome($input['nome']);
            $produto->setPreco((float)$input['preco']);

            // Salva as alterações no banco via controller
            $success = $controller->salvar($produto);

            if ($success) {
                echo json_encode(['message' => 'Produto atualizado'], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Falha ao atualizar produto'], JSON_UNESCAPED_UNICODE);
            }
        } catch (InvalidArgumentException $ex) {
            // Captura exceções de validação e retorna erro 400
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'DELETE':
        // Para deletar, é obrigatório informar o ID via query string
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID necessário para deletar'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Tenta deletar o produto pelo ID informado
        $success = $controller->deletar($id);

        if ($success) {
            echo json_encode(['message' => 'Produto deletado'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao deletar produto'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    default:
        // Retorna erro 405 para métodos HTTP não permitidos
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
        exit;
}
