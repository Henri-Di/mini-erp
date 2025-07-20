<?php
require_once __DIR__.'/../models/PedidoProduto.php';

class PedidoProdutoController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function salvar(PedidoProduto $pedidoProduto): bool
    {
        if ($pedidoProduto->getId()) {
            $sql = "UPDATE pedido_produto SET pedido_id = :pedidoId, produto_id = :produtoId, variacao = :variacao, quantidade = :quantidade, preco_unitario = :precoUnitario WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':pedidoId' => $pedidoProduto->getPedidoId(),
                ':produtoId' => $pedidoProduto->getProdutoId(),
                ':variacao' => $pedidoProduto->getVariacao(),
                ':quantidade' => $pedidoProduto->getQuantidade(),
                ':precoUnitario' => $pedidoProduto->getPrecoUnitario(),
                ':id' => $pedidoProduto->getId()
            ]);
        } else {
            $sql = "INSERT INTO pedido_produto (pedido_id, produto_id, variacao, quantidade, preco_unitario) VALUES (:pedidoId, :produtoId, :variacao, :quantidade, :precoUnitario)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':pedidoId' => $pedidoProduto->getPedidoId(),
                ':produtoId' => $pedidoProduto->getProdutoId(),
                ':variacao' => $pedidoProduto->getVariacao(),
                ':quantidade' => $pedidoProduto->getQuantidade(),
                ':precoUnitario' => $pedidoProduto->getPrecoUnitario()
            ]);
            if ($result) {
                $pedidoProduto->setId((int)$this->conn->lastInsertId());
            }
            return $result;
        }
    }

    public function buscarPorId(int $id): ?PedidoProduto
    {
        $stmt = $this->conn->prepare("SELECT * FROM pedido_produto WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new PedidoProduto(
            (int)$row['pedido_id'],
            (int)$row['produto_id'],
            $row['variacao'],
            (int)$row['quantidade'],
            (float)$row['preco_unitario'],
            (int)$row['id']
        );
    }

    public function listarPorPedidoId(int $pedidoId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM pedido_produto WHERE pedido_id = :pedidoId");
        $stmt->execute([':pedidoId' => $pedidoId]);
        $lista = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM pedido_produto WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
