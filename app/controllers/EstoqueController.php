<?php
require_once __DIR__.'/../models/Estoque.php';

class EstoqueController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function salvar(Estoque $estoque): bool
    {
        if ($estoque->getId()) {
            $sql = "UPDATE estoque SET produto_id = :produtoId, variacao = :variacao, quantidade = :quantidade WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':produtoId' => $estoque->getProdutoId(),
                ':variacao' => $estoque->getVariacao(),
                ':quantidade' => $estoque->getQuantidade(),
                ':id' => $estoque->getId()
            ]);
        } else {
            $sql = "INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (:produtoId, :variacao, :quantidade)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':produtoId' => $estoque->getProdutoId(),
                ':variacao' => $estoque->getVariacao(),
                ':quantidade' => $estoque->getQuantidade()
            ]);
            if ($result) {
                $estoque->setId((int)$this->conn->lastInsertId());
            }
            return $result;
        }
    }

    public function buscarPorId(int $id): ?Estoque
    {
        $stmt = $this->conn->prepare("SELECT * FROM estoque WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Estoque((int)$row['produto_id'], $row['variacao'], (int)$row['quantidade'], (int)$row['id']);
    }

    public function listarPorProdutoId(int $produtoId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM estoque WHERE produto_id = :produtoId");
        $stmt->execute([':produtoId' => $produtoId]);
        $estoques = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $estoques[] = new Estoque((int)$row['produto_id'], $row['variacao'], (int)$row['quantidade'], (int)$row['id']);
        }
        return $estoques;
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM estoque WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
