<?php
// Inclusão dos arquivos necessários: configuração, conexão com banco, modelo Pedido e controller PedidoController
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../controllers/PedidoController.php';

// Define o cabeçalho para que a resposta seja em JSON com codificação UTF-8
header('Content-Type: application/json; charset=utf-8');

// Criação da conexão PDO com o banco de dados MySQL utilizando constantes definidas em config.php
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=mini_erp;charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]  // Configura o PDO para lançar exceções em erros
    );
} catch (PDOException $e) {
    // Caso ocorra erro na conexão, retorna status 500 com mensagem amigável em JSON e encerra o script
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

// Cria um wrapper Database que abstrai o acesso ao PDO (injeção de dependência)
$db = new Database($pdo);

// Este endpoint aceita apenas requisições POST para atualização/cancelamento do pedido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Retorna erro 405 (método não permitido) caso outro método seja usado
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Lê os dados JSON enviados no corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);

// Validação básica dos dados recebidos: campos 'id' e 'status' são obrigatórios
if (!isset($input['id'], $input['status'])) {
    http_response_code(400); // 400 Bad Request
    echo json_encode(['error' => 'ID e status são obrigatórios']);
    exit;
}

// Sanitiza e converte os dados recebidos
$id = (int)$input['id'];
$status = trim(strtolower($input['status']));

// Instancia o controller PedidoController para manipular os pedidos no banco
$pedidoController = new PedidoController($db);

try {
    // Busca o pedido pelo ID informado
    $pedido = $pedidoController->buscarPorId($id);

    // Se não existir, retorna 404 Not Found
    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido não encontrado']);
        exit;
    }

    // Se o status recebido for 'cancelado', então o pedido será removido do banco
    if ($status === 'cancelado') {
        if ($pedidoController->deletar($id)) {
            // Sucesso ao deletar o pedido cancelado
            echo json_encode(['message' => 'Pedido cancelado e removido com sucesso']);
        } else {
            // Erro interno ao tentar deletar o pedido
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar pedido']);
        }
    } else {
        // Caso contrário, atualiza o status do pedido com o valor informado
        $pedido->setStatus($status);

        // Tenta salvar a alteração no banco
        if ($pedidoController->salvar($pedido)) {
            echo json_encode(['message' => 'Status do pedido atualizado para: ' . $status]);
        } else {
            // Caso ocorra erro na atualização
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar status do pedido']);
        }
    }
} catch (Exception $e) {
    // Captura erros inesperados no processamento e retorna erro 500 com mensagem
    http_response_code(500);
    echo json_encode(['error' => 'Erro no processamento: ' . $e->getMessage()]);
}
