<?php

class App
{
    // Nome do controller padrão (caso a URL não especifique outro)
    protected string $controllerName = 'HomeController';

    // Instância do controller carregado dinamicamente
    protected object $controllerInstance;

    // Método padrão a ser chamado no controller
    protected string $method = 'index';

    // Parâmetros que serão passados para o método do controller
    protected array $params = [];

    /**
     * Construtor da aplicação.
     * Responsável por analisar a URL, carregar o controller correto,
     * identificar o método e passar os parâmetros para a ação.
     */
    public function __construct()
    {
        // Obtém a URL limpa e segmentada em array
        $url = $this->getUrl();

        // Se foi especificado um controller na URL
        if (!empty($url[0])) {
            // Monta o caminho do arquivo do controller esperado
            $controllerPath = '../app/controllers/' . ucfirst($url[0]) . 'Controller.php';

            // Verifica se o arquivo do controller existe
            if (file_exists($controllerPath)) {
                // Atualiza o nome do controller para o especificado na URL
                $this->controllerName = ucfirst($url[0]) . 'Controller';
                // Remove o segmento do controller da URL para facilitar manipulação dos demais índices
                unset($url[0]);
            } else {
                // Controller não encontrado: retorna erro 404 e encerra execução
                http_response_code(404);
                exit("Controller '{$url[0]}' não encontrado.");
            }
        }

        // Inclui o arquivo do controller que será instanciado
        require_once '../app/controllers/' . $this->controllerName . '.php';

        // Cria a instância do controller para ser utilizada
        $this->controllerInstance = new $this->controllerName;

        // Verifica se existe um método definido na URL e se ele é chamável no controller
        if (isset($url[1]) && is_callable([$this->controllerInstance, $url[1]])) {
            $this->method = $url[1]; // Define o método a ser chamado
            unset($url[1]);          // Remove o segmento do método para ajustar os parâmetros
        }

        // Reorganiza os parâmetros (remove gaps dos unset) para passar para o método
        $this->params = $url ? array_values($url) : [];

        // Executa o método do controller passando os parâmetros
        call_user_func_array([$this->controllerInstance, $this->method], $this->params);
    }

    /**
     * Extrai e limpa a URL fornecida via GET 'url'.
     * Remove barras extras e caracteres inválidos para segurança.
     *
     * @return array Segmentos da URL em formato de array
     */
    public function getUrl(): array
    {
        if (isset($_GET['url'])) {
            // Remove barras finais, sanitiza e divide em segmentos separados por '/'
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        // Retorna array vazio se nenhum parâmetro URL for passado
        return [];
    }
}
