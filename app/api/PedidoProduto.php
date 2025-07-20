<?php
// Inclusão dos arquivos essenciais: configuração, conexão com banco, modelo PedidoProduto e controller correspondente
require_once __DIR__.'/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/PedidoProduto.php';
require_once __DIR__ . '/../controllers/PedidoProdutoController.php';

// Define o cabeçalho da resposta para JSON UTF-8
header('Content-Type: application/json; charset=utf-8');

// Tenta estabelecer a conexão com o banco de dados
try {
    $db = new Database();
} catch (Exception $e) {
    // Caso ocorra erro na conexão, responde com código 500 e mensagem de erro
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco: ' . $e->getMessage()]);
    exit;
}

// Cria a instância do controller que gerencia os itens de pedido (PedidoProduto)
$controller = new PedidoProdutoController($db);

// Captura o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// Estrutura condicional para tratar os diferentes métodos HTTP
switch ($method) {

    case 'GET':
        // Se informado parâmetro id, busca um item específico pelo seu ID
        if (isset($_GET['id'])) {
            $item = $controller->buscarPorId((int)$_GET['id']);
            if ($item) {
                // Retorna o item encontrado em formato JSON (array)
                echo json_encode($item->toArray());
            } else {
                // Caso item não exista, responde com 404 Not Found
                http_response_code(404);
                echo json_encode(['error' => 'Item não encontrado']);
            }
        }
        // Se informado parâmetro pedido_id, lista todos os itens desse pedido
        elseif (isset($_GET['pedido_id'])) {
            $lista = $controller->listarPorPedidoId((int)$_GET['pedido_id']);
            // Converte lista de objetos em arrays para JSON
            $data = array_map(fn($p) => $p->toArray(), $lista);
            echo json_encode($data);
        }
        // Se nenhum parâmetro obrigatório foi passado, retorna erro 400 Bad Request
        else {
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro id ou pedido_id obrigatório']);
        }
        break;

    case 'POST':
        // Lê dados JSON do corpo da requisição para criação de novo item
        $data = json_decode(file_get_contents('php://input'), true);

        // Valida campos obrigatórios para criação
        if (
            !isset($data['pedidoId'], $data['produtoId'], $data['variacao'], $data['quantidade'], $data['precoUnitario'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }

        try {
            // Cria novo objeto PedidoProduto com os dados recebidos
            $item = new PedidoProduto(
                (int)$data['pedidoId'],
                (int)$data['produtoId'],
                $data['variacao'],
                (int)$data['quantidade'],
                (float)$data['precoUnitario']
            );

            // Tenta salvar o item e responde conforme sucesso ou falha
            if ($controller->salvar($item)) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Item criado', 'id' => $item->getId()]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao salvar item']);
            }
        } catch (InvalidArgumentException $ex) {
            // Caso dados sejam inválidos, captura exceção e retorna erro 400 com mensagem
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()]);
        }
        break;

    case 'PUT':
        // Lê dados JSON do corpo da requisição para atualização de item existente
        $data = json_decode(file_get_contents('php://input'), true);

        // Valida campos obrigatórios, incluindo o ID do item para atualização
        if (
            !isset($data['id'], $data['pedidoId'], $data['produtoId'], $data['variacao'], $data['quantidade'], $data['precoUnitario'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos']);
            exit;
        }

        try {
            // Cria objeto PedidoProduto com ID para atualizar o registro existente
            $item = new PedidoProduto(
                (int)$data['pedidoId'],
                (int)$data['produtoId'],
                $data['variacao'],
                (int)$data['quantidade'],
                (float)$data['precoUnitario'],
                (int)$data['id']
            );

            // Tenta salvar atualização e retorna resposta adequada
            if ($controller->salvar($item)) {
                echo json_encode(['message' => 'Item atualizado']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar item']);
            }
        } catch (InvalidArgumentException $ex) {
            // Captura erros de validação na atualização e retorna erro 400
            http_response_code(400);
            echo json_encode(['error' => $ex->getMessage()]);
        }
        break;

    case 'DELETE':
        // Lê dados enviados no corpo da requisição para identificar item a deletar
        parse_str(file_get_contents('php://input'), $input);

        // Verifica se ID foi informado para exclusão
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório para deletar']);
            exit;
        }

        // Tenta deletar o item pelo ID e retorna resposta de sucesso ou erro
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Item deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar item']);
        }
        break;

    default:
        // Para métodos HTTP não suportados, retorna erro 405 Method Not Allowed
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
