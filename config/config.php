<?php
// Configuração de timezone
date_default_timezone_set('America/Sao_Paulo');

// Defina o ambiente (dev ou production)
define('ENVIRONMENT', 'dev'); // Ambiente local = dev

// URL base do projeto (local, porta 8181)
define('BASE_URL', 'http://localhost:8181/mini-erp/public');

// Caminho da pasta raiz
define('ROOT_PATH', dirname(__DIR__));

// Configurações do banco de dados local
define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_erp');
define('DB_USER', 'root');
define('DB_PASS', ''); 

// Mostrar ou ocultar erros
if (ENVIRONMENT === 'dev') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
}
