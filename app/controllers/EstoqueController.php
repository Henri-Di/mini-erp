<?php
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../core/Database.php';

class EstoqueController
{
    // Instância do wrapper Database para execução de queries
    private Database $db;

    /**
     * Construtor recebe a instância Database para injeção de dependência.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Salva um registro de estoque no banco.
     * Se o objeto Estoque possui ID, realiza UPDATE,
     * senão realiza INSERT e atualiza o ID do objeto.
     *
     * @param Estoque $estoque
     * @return bool Retorna true se operação foi bem-sucedida, false caso contrário.
     */
    public function salvar(Estoque $estoque): bool
    {
        if ($estoque->getId()) {
            // Atualiza estoque existente identificado pelo ID
            $this->db->query("UPDATE estoque SET produto_id = :produtoId, variacao = :variacao, quantidade = :quantidade WHERE id = :id");
            $this->db->bind(':produtoId', $estoque->getProdutoId());
            $this->db->bind(':variacao', $estoque->getVariacao());
            $this->db->bind(':quantidade', $estoque->getQuantidade());
            $this->db->bind(':id', $estoque->getId());

            return $this->db->execute();
        } else {
            // Insere novo registro de estoque
            $this->db->query("INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (:produtoId, :variacao, :quantidade)");
            $this->db->bind(':produtoId', $estoque->getProdutoId());
            $this->db->bind(':variacao', $estoque->getVariacao());
            $this->db->bind(':quantidade', $estoque->getQuantidade());

            $result = $this->db->execute();

            // Atualiza ID do objeto Estoque com o último ID inserido no banco
            if ($result) {
                $estoque->setId((int)$this->db->lastInsertId());
            }

            return $result;
        }
    }

    /**
     * Busca um registro de estoque pelo seu ID.
     *
     * @param int $id ID do estoque
     * @return Estoque|null Retorna objeto Estoque ou null se não encontrado.
     */
    public function buscarPorId(int $id): ?Estoque
    {
        $this->db->query("SELECT * FROM estoque WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        if (!$row) {
            return null;
        }

        // Retorna objeto Estoque com dados do banco
        return new Estoque(
            (int)$row['produto_id'],
            $row['variacao'],
            (int)$row['quantidade'],
            (int)$row['id']
        );
    }

    /**
     * Lista todos os registros de estoque para um dado produto.
     *
     * @param int $produtoId ID do produto
     * @return Estoque[] Array de objetos Estoque relacionados ao produto
     */
    public function listarPorProdutoId(int $produtoId): array
    {
        $this->db->query("SELECT * FROM estoque WHERE produto_id = :produtoId");
        $this->db->bind(':produtoId', $produtoId);
        $resultados = $this->db->resultSet();

        $estoques = [];

        // Converte cada linha retornada em um objeto Estoque
        foreach ($resultados as $row) {
            $estoques[] = new Estoque(
                (int)$row['produto_id'],
                $row['variacao'],
                (int)$row['quantidade'],
                (int)$row['id']
            );
        }

        return $estoques;
    }

    /**
     * Deleta um registro de estoque pelo ID.
     *
     * @param int $id ID do estoque a ser removido
     * @return bool Retorna true se deletado com sucesso, false caso contrário.
     */
    public function deletar(int $id): bool
    {
        $this->db->query("DELETE FROM estoque WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }
}
