<?php

class Controller
{
    // Diretórios base para views e models, fácil alteração se necessário
    protected string $viewsPath = __DIR__ . '/../views/';
    protected string $modelsPath = __DIR__ . '/../models/';

    /**
     * Carrega uma view com os dados.
     *
     * @param string $view Nome da view (ex: 'produto/index')
     * @param array $data Array de dados passados para a view
     * @throws Exception Se a view não for encontrada
     */
    public function view(string $view, array $data = []): void
    {
        $viewPath = realpath($this->viewsPath . $view . '.php');

        if (!$viewPath || !file_exists($viewPath)) {
            $this->logError("View não encontrada: $viewPath");
            http_response_code(404);
            throw new Exception("A view solicitada não foi encontrada.");
        }

        extract($data);

        try {
            require $viewPath;
        } catch (Throwable $e) {
            $this->logError("Erro ao carregar a view '$view': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Carrega um model (classe de regra de negócio / persistência).
     *
     * @param string $model Nome do model (ex: 'Produto')
     * @param array $constructorArgs Argumentos para o construtor do model (opcional)
     * @return object Instância do model
     * @throws Exception Se o model ou a classe não forem encontrados
     */
    public function model(string $model, array $constructorArgs = []): object
    {
        $modelPath = realpath($this->modelsPath . $model . '.php');

        if (!$modelPath || !file_exists($modelPath)) {
            $this->logError("Model não encontrado: $modelPath");
            http_response_code(404);
            throw new Exception("O model solicitado não foi encontrado.");
        }

        require_once $modelPath;

        if (!class_exists($model)) {
            $this->logError("Classe do model não encontrada: $model");
            throw new Exception("A classe do model '$model' não foi encontrada.");
        }

        // Suporta injeção de dependência via reflexão e passagem de parâmetros ao construtor
        if (empty($constructorArgs)) {
            return new $model();
        }

        $reflection = new ReflectionClass($model);
        return $reflection->newInstanceArgs($constructorArgs);
    }

    /**
     * Registra erros em arquivo de log (básico).
     *
     * @param string $message Mensagem de erro
     * @return void
     */
    protected function logError(string $message): void
    {
        $logFile = __DIR__ . '/../logs/error.log';
        $date = date('Y-m-d H:i:s');
        error_log("[$date] $message" . PHP_EOL, 3, $logFile);
    }
}
