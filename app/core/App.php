<?php

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // Verifica se o controller existe
        if (!empty($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        // Inclui e instancia o controller
        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Verifica se o método existe no controller
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // Os parâmetros são os valores restantes da URL
        $this->params = $url ? array_values($url) : [];

        // Chama o método com os parâmetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // Quebra a URL em partes (ex: /produto/editar/1)
    public function getUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
