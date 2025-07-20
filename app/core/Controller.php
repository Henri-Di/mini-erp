<?php

class Controller
{
    /**
     * Carrega uma view com os dados.
     *
     * @param string $view Nome da view (ex: 'produto/index')
     * @param array $data Array de dados passados para a view
     */
    public function view(string $view, array $data = [])
    {
        $viewPath = '../app/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("A view <strong>$viewPath</strong> não foi encontrada.");
        }
    }

    /**
     * Carrega um model (classe de regra de negócio / persistência).
     *
     * @param string $model Nome do model (ex: 'Produto')
     * @return object Instância do model
     */
    public function model(string $model)
    {
        $modelPath = '../app/models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die("O model <strong>$modelPath</strong> não foi encontrado.");
        }
    }
}
