<?php
require_once __DIR__.'/../models/PedidoProduto.php';
require_once __DIR__.'/../core/Database.php';

class PedidoProdutoController
{
    // Instância do Database para executar as operações no banco
    private Database $db;

    /**
     * Construtor recebe uma instância de Database para manipulação dos dados.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Salva um objeto PedidoProduto no banco.
     * Se o objeto possuir ID, realiza UPDATE; caso contrário, realiza INSERT.
     *
     * @param PedidoProduto $pedidoProduto
     * @return bool Retorna true se a operação foi bem-sucedida, false caso contrário.
     */
    public function salvar(PedidoProduto $pedidoProduto): bool
    {
        if ($pedidoProduto->getId()) {
            // Atualiza um item existente associado a um pedido
            $this->db->query(
                "UPDATE pedido_produto 
                 SET pedido_id = :pedidoId, produto_id = :produtoId, variacao = :variacao, quantidade = :quantidade, preco_unitario = :precoUnitario 
                 WHERE id = :id"
            );
            $this->db->bind(':pedidoId', $pedidoProduto->getPedidoId(), PDO::PARAM_INT);
            $this->db->bind(':produtoId', $pedidoProduto->getProdutoId(), PDO::PARAM_INT);
            $this->db->bind(':variacao', $pedidoProduto->getVariacao());
            $this->db->bind(':quantidade', $pedidoProduto->getQuantidade(), PDO::PARAM_INT);
            $this->db->bind(':precoUnitario', $pedidoProduto->getPrecoUnitario());
            $this->db->bind(':id', $pedidoProduto->getId(), PDO::PARAM_INT);

            return $this->db->execute();
        } else {
            // Insere um novo item associado a um pedido
            $this->db->query(
                "INSERT INTO pedido_produto (pedido_id, produto_id, variacao, quantidade, preco_unitario) 
                 VALUES (:pedidoId, :produtoId, :variacao, :quantidade, :precoUnitario)"
            );
            $this->db->bind(':pedidoId', $pedidoProduto->getPedidoId(), PDO::PARAM_INT);
            $this->db->bind(':produtoId', $pedidoProduto->getProdutoId(), PDO::PARAM_INT);
            $this->db->bind(':variacao', $pedidoProduto->getVariacao());
            $this->db->bind(':quantidade', $pedidoProduto->getQuantidade(), PDO::PARAM_INT);
            $this->db->bind(':precoUnitario', $pedidoProduto->getPrecoUnitario());

            $result = $this->db->execute();

            // Atualiza o ID do objeto após inserção bem-sucedida
            if ($result) {
                $pedidoProduto->setId((int)$this->db->lastInsertId());
            }

            return $result;
        }
    }

    /**
     * Busca um item pedido-produto pelo seu ID único.
     *
     * @param int $id ID do item pedido-produto
     * @return PedidoProduto|null Retorna o objeto PedidoProduto ou null se não encontrado.
     */
    public function buscarPorId(int $id): ?PedidoProduto
    {
        $this->db->query("SELECT * FROM pedido_produto WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $row = $this->db->single();

        if (!$row) {
            // Retorna null caso o item não exista no banco
            return null;
        }

        // Retorna o objeto PedidoProduto com os dados carregados
        return new PedidoProduto(
            (int)$row['pedido_id'],
            (int)$row['produto_id'],
            $row['variacao'],
            (int)$row['quantidade'],
            (float)$row['preco_unitario'],
            (int)$row['id']
        );
    }

    /**
     * Lista todos os itens associados a um pedido específico.
     *
     * @param int $pedidoId ID do pedido
     * @return PedidoProduto[] Array de objetos PedidoProduto relacionados ao pedido.
     */
    public function listarPorPedidoId(int $pedidoId): array
    {
        $this->db->query("SELECT * FROM pedido_produto WHERE pedido_id = :pedidoId");
        $this->db->bind(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $resultados = $this->db->resultSet();

        $lista = [];

        // Converte cada linha do resultado em um objeto PedidoProduto
        foreach ($resultados as $row) {
            $lista[] = new PedidoProduto(
                (int)$row['pedido_id'],
                (int)$row['produto_id'],
                $row['variacao'],
                (int)$row['quantidade'],
                (float)$row['preco_unitario'],
                (int)$row['id']
            );
        }

        return $lista;
    }

    /**
     * Deleta um item pedido-produto pelo seu ID.
     *
     * @param int $id ID do item a ser removido
     * @return bool Retorna true se a remoção for bem-sucedida, false caso contrário.
     */
    public function deletar(int $id): bool
    {
        $this->db->query("DELETE FROM pedido_produto WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }
}
