<?php
// Inclusão dos arquivos de configuração, classes de conexão e modelos/controllers necessários
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../controllers/CupomController.php';

// Tenta estabelecer conexão PDO com o banco de dados MySQL usando configurações do arquivo config.php
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=mini_erp;charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Lança exceções em erros PDO
    ]);
} catch (PDOException $e) {
    // Se falhar a conexão, retorna erro 500 com mensagem JSON e encerra execução
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com banco']);
    exit;
}

// Instancia objeto Database com a conexão PDO
$db = new Database($pdo);

// Cria o controller de Cupons, responsável por manipular regras de negócio
$controller = new CupomController($db);

// Captura o método HTTP da requisição para decidir a ação
$method = $_SERVER['REQUEST_METHOD'];

// Define o cabeçalho de resposta para JSON com codificação UTF-8
header('Content-Type: application/json; charset=utf-8');

// Tratamento das requisições com base no método HTTP
switch ($method) {
    case 'GET':
        // Se for passado um código via query string, busca cupom específico
        if (isset($_GET['codigo'])) {
            $codigo = $_GET['codigo'];
            $cupom = $controller->buscarPorCodigo($codigo);

            if ($cupom) {
                // Retorna dados do cupom encontrado no formato JSON
                echo json_encode([
                    'id' => $cupom->getId(),
                    'codigo' => $cupom->getCodigo(),
                    'desconto' => $cupom->getDesconto(),
                    'minimo_subtotal' => $cupom->getMinimoSubtotal(),
                    'validade' => $cupom->getValidade()
                ]);
            } else {
                // Cupom não encontrado: resposta 404 com mensagem de erro
                http_response_code(404);
                echo json_encode(['error' => 'Cupom não encontrado']);
            }
        } else {
            // Sem código, lista todos os cupons cadastrados
            $cupons = $controller->listarTodos();

            // Mapeia cada objeto Cupom para array simples para JSON
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
        // Recebe os dados JSON enviados no corpo da requisição para criação de novo cupom
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação básica dos dados obrigatórios
        if (
            !is_array($data) ||
            !isset($data['codigo'], $data['desconto'], $data['minimo_subtotal'], $data['validade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        // Cria nova entidade Cupom com os dados recebidos
        $cupom = new Cupom(
            $data['codigo'],
            (float)$data['desconto'],
            (float)$data['minimo_subtotal'],
            $data['validade']
        );

        // Tenta salvar o cupom usando o controller e responde conforme sucesso/falha
        if ($controller->salvar($cupom)) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Cupom criado', 'id' => $cupom->getId()]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao salvar cupom']);
        }
        break;

    case 'PUT':
        // Recebe dados JSON para atualizar cupom existente
        $data = json_decode(file_get_contents('php://input'), true);

        // Validação dos dados necessários para atualização, incluindo ID
        if (
            !is_array($data) ||
            !isset($data['id'], $data['codigo'], $data['desconto'], $data['minimo_subtotal'], $data['validade'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou incompletos']);
            exit;
        }

        // Cria entidade Cupom com ID para atualizar
        $cupom = new Cupom(
            $data['codigo'],
            (float)$data['desconto'],
            (float)$data['minimo_subtotal'],
            $data['validade'],
            (int)$data['id']
        );

        // Tenta atualizar o cupom e responde conforme resultado
        if ($controller->salvar($cupom)) {
            echo json_encode(['message' => 'Cupom atualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar cupom']);
        }
        break;

    case 'DELETE':
        // Para DELETE, lê dados do corpo (php://input) e converte para array
        parse_str(file_get_contents('php://input'), $input);

        // ID é obrigatório para exclusão
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID obrigatório']);
            exit;
        }

        // Tenta deletar cupom pelo ID e responde conforme resultado
        if ($controller->deletar((int)$input['id'])) {
            echo json_encode(['message' => 'Cupom deletado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar cupom']);
        }
        break;

    default:
        // Método HTTP não suportado - retorna erro 405 (Method Not Allowed)
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
