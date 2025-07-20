<?php
// --------------------------------------------------
// Arquivo de bootstrap do sistema
// Responsável por carregar as configurações iniciais,
// classes essenciais e iniciar o framework MVC customizado.
// --------------------------------------------------

// Carrega arquivo de configuração geral do projeto (banco, URLs, etc)
require_once __DIR__ . '/../config/config.php';

// Inclui as classes básicas do core do sistema:
// - App: controlador central da aplicação, gerencia rotas e requests
// - Controller: classe base para todos os controllers da aplicação
// - Database: gerenciador da conexão com o banco de dados via PDO
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Database.php';

// Autoload PSR-4 simples para carregar Models e Controllers dinamicamente
// Sempre que uma classe for instanciada e não estiver carregada,
// essa função tentará localizar o arquivo correspondente nos diretórios
// 'app/models' ou 'app/controllers' para facilitar organização e evitar
// múltiplos require_once manuais.
spl_autoload_register(function ($class) {
    $modelPath = __DIR__ . '/../app/models/' . $class . '.php';
    $controllerPath = __DIR__ . '/../app/controllers/' . $class . '.php';

    if (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
});

// Instancia a aplicação e dispara o fluxo principal do framework MVC
// Isso processa a URL, mapeia para controller/action, executa lógica,
// renderiza views e retorna a resposta para o cliente.
$app = new App();
