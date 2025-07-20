<?php
// --------------------------------------------------
// Configurações globais do projeto Mini ERP
// Arquivo responsável por definir constantes essenciais,
// configurações de ambiente, banco de dados e timezone.
// --------------------------------------------------

// Define o fuso horário padrão da aplicação para evitar avisos
// e garantir consistência na manipulação de datas e horários.
date_default_timezone_set('America/Sao_Paulo');

// --------------------------------------------------
// Definição do ambiente de execução
// Pode ser 'dev' para desenvolvimento local ou 'production'
// para ambiente de produção. Essa definição controla exibição
// de erros, logs e comportamentos específicos.
// --------------------------------------------------
define('ENVIRONMENT', 'dev'); // Alterar para 'production' na implantação

// --------------------------------------------------
// URL base do projeto
// Endereço base onde a aplicação está hospedada.
// Ajustar conforme o domínio ou porta em uso.
// --------------------------------------------------
define('BASE_URL', 'http://localhost:8181/mini-erp/public');

// --------------------------------------------------
// Caminho absoluto da raiz do projeto no servidor
// Útil para inclusão de arquivos e referências internas.
// --------------------------------------------------
define('ROOT_PATH', dirname(__DIR__));

// --------------------------------------------------
// Configurações do banco de dados
// Parâmetros de conexão com o MySQL (ou outro SGBD).
// Deve ser ajustado conforme o ambiente e credenciais.
// --------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_erp');
define('DB_USER', 'root');
define('DB_PASS', ''); // Nunca deixe senha vazia em produção!

// --------------------------------------------------
// Configuração da exibição de erros e relatórios PHP
// Em ambiente de desenvolvimento, todos os erros serão exibidos
// para facilitar debug. Em produção, erros ficam ocultos para
// evitar vazamento de informações sensíveis.
// --------------------------------------------------
if (ENVIRONMENT === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// --------------------------------------------------
// Configurações adicionais e boas práticas podem ser adicionadas aqui,
// como:
// - Definição de timeouts
// - Configurações de logs
// - Controle de cache
// - Configurações específicas para produção
// --------------------------------------------------
