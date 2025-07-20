<?php
// Inclusão dos arquivos essenciais: configuração, conexão com banco, modelo Pedido e controller
require_once __DIR__.'/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../controllers/PedidoController.php';

// Configurações dos cabeçalhos HTTP para permitir CORS e definir tipo de conteúdo JSON
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem (ideal para dev)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Métodos permitidos
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Cabeçalhos permitidos
header('Content-Type: application/json; charset=utf-8'); // Tipo de conteúdo JSON UTF-8

// Responde imediatamente para requisições OPTIONS (preflight do CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Tenta criar conexão com banco de dados usando Database (PDO encapsulado)
try {
    $db = new Database();
} catch (Exception $e) {
    // Caso falhe conexão, retorna erro 500 com mensagem JSON
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Instancia o controller de Pedidos, responsável pela lógica de negócio
$controller = new PedidoController($db);

// Obtém o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Obtém o parâmetro id da query string, se existir (para buscar/atualizar/deletar por ID)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Switch para tratar os diferentes métodos HTTP
switch ($method) {

    case 'GET':
        if ($id) {
            // Busca pedido específico pelo ID
            $pedido = $controller->buscarPorId($id);
            if ($pedido) {
                // Retorna pedido como array JSON
                echo json_encode($pedido->toArray(), JSON_UNESCAPED_UNICODE);
            } else {
                // Pedido não encontrado: status 404
                http_response_code(404);
                echo json_encode(['error' => 'Pedido não encontrado'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Sem ID, lista todos os pedidos
            $pedidos = $controller->listarTodos();

            // Converte lista de objetos Pedido para array simples para JSON
            $data = array_map(fn($p) => $p->toArray(), $pedidos);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'POST':
        // Lê dados JSON do corpo da requisição para criação de novo pedido
        $data = json_decode(file_get_contents('php://input'), true);

        // Valida presença dos campos essenciais
        if (
            !$data || !isset($data['valorTotal'], $data['frete'], $data['endereco'], $data['cep'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Cria novo objeto Pedido com dados fornecidos (status padrão 'pendente')
            $pedido = new Pedido(
                (float)$data['valorTotal'],
                (float)$data['frete'],
                $data['endereco'],
                $data['cep'],
                $data['status'] ?? 'pendente'
            );

            // Tenta salvar o pedido no banco
            $success = $controller->salvar($pedido);

            if ($success) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Pedido criado', 'id' => $pedido->getId()], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao salvar pedido'], JSON_UNESCAPED_UNICODE);
            }
        } catch (InvalidArgumentException $ex) {
            // Captura erros de validação no construtor/setters
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'PUT':
        // Para atualização, ID deve ser informado via query string
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID necessário para atualizar'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Lê dados JSON para atualização
        $data = json_decode(file_get_contents('php://input'), true);

        // Valida campos obrigatórios para atualização
        if (
            !$data || !isset($data['valorTotal'], $data['frete'], $data['endereco'], $data['cep'], $data['status'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Busca pedido atual para atualizar seus dados
        $pedido = $controller->buscarPorId($id);
        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['error' => 'Pedido não encontrado'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // Atualiza propriedades do pedido com os dados recebidos
            $pedido->setValorTotal((float)$data['valorTotal']);
            $pedido->setFrete((float)$data['frete']);
            $pedido->setEndereco($data['endereco']);
            $pedido->setCep($data['cep']);
            $pedido->setStatus($data['status']);

            // Salva alterações no banco
            $success = $controller->salvar($pedido);
            if ($success) {
                echo json_encode(['message' => 'Pedido atualizado'], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar pedido'], JSON_UNESCAPED_UNICODE);
            }
        } catch (InvalidArgumentException $ex) {
            // Captura erros de validação na atualização
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;

    case 'DELETE':
        // Para DELETE, lê dados enviados no corpo da requisição (php://input)
        parse_str(file_get_contents('php://input'), $input);

        // Valida presença do ID para exclusão
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório para deletar'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Tenta deletar pedido pelo ID e responde conforme resultado
        $success = $controller->deletar((int)$input['id']);
        if ($success) {
            echo json_encode(['message' => 'Pedido deletado'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar pedido'], JSON_UNESCAPED_UNICODE);
        }
        exit;

    default:
        // Caso método HTTP não seja suportado, retorna erro 405
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
        exit;
}
