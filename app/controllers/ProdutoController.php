<?php
require_once __DIR__.'/../models/Produto.php';
require_once __DIR__.'/../core/Database.php';

class ProdutoController
{
    // Instância da classe Database para interagir com o banco de dados
    private Database $db;

    /**
     * Construtor recebe a instância de Database para execução das queries
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Salva um produto no banco.
     * Se o produto já possui ID, faz UPDATE.
     * Caso contrário, realiza INSERT e atualiza o objeto com o ID gerado.
     *
     * @param Produto $produto
     * @return bool True se operação foi bem-sucedida, false caso contrário
     */
    public function salvar(Produto $produto): bool
    {
        if ($produto->getId()) {
            // Atualiza um produto existente
            $this->db->query("UPDATE produtos SET nome = :nome, preco = :preco WHERE id = :id");
            $this->db->bind(':nome', $produto->getNome());
            $this->db->bind(':preco', $produto->getPreco());
            $this->db->bind(':id', $produto->getId(), PDO::PARAM_INT);

            return $this->db->execute();
        } else {
            // Insere um novo produto com data de criação atual
            $this->db->query("INSERT INTO produtos (nome, preco, criado_em) VALUES (:nome, :preco, NOW())");
            $this->db->bind(':nome', $produto->getNome());
            $this->db->bind(':preco', $produto->getPreco());

            $result = $this->db->execute();

            // Atualiza o objeto com o ID gerado pelo banco após inserção
            if ($result) {
                $produto->setId((int)$this->db->lastInsertId());
            }

            return $result;
        }
    }

    /**
     * Busca um produto pelo seu ID.
     *
     * @param int $id ID do produto
     * @return Produto|null Retorna o produto encontrado ou null caso não exista
     */
    public function buscarPorId(int $id): ?Produto
    {
        $this->db->query("SELECT * FROM produtos WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        $row = $this->db->single();

        if (!$row) {
            // Retorna null se produto não encontrado
            return null;
        }

        // Retorna objeto Produto preenchido com dados do banco
        return new Produto($row['nome'], (float)$row['preco'], (int)$row['id'], $row['criado_em']);
    }

    /**
     * Retorna uma lista de todos os produtos cadastrados, ordenados pela data de criação (mais recentes primeiro).
     *
     * @return Produto[] Array de objetos Produto
     */
    public function listarTodos(): array
    {
        $this->db->query("SELECT * FROM produtos ORDER BY criado_em DESC");
        $resultados = $this->db->resultSet();

        $produtos = [];

        // Converte cada linha do resultado em um objeto Produto
        foreach ($resultados as $row) {
            $produtos[] = new Produto($row['nome'], (float)$row['preco'], (int)$row['id'], $row['criado_em']);
        }

        return $produtos;
    }

    /**
     * Remove um produto pelo seu ID.
     *
     * @param int $id ID do produto a ser removido
     * @return bool True se remoção foi bem-sucedida, false caso contrário
     */
    public function deletar(int $id): bool
    {
        $this->db->query("DELETE FROM produtos WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }
}
