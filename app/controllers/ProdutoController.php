<?php
require_once __DIR__.'/../models/Produto.php';

class ProdutoController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Salvar ou atualizar produto
    public function salvar(Produto $produto): bool
    {
        if ($produto->getId()) {
            $sql = "UPDATE produtos SET nome = :nome, preco = :preco WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':nome' => $produto->getNome(),
                ':preco' => $produto->getPreco(),
                ':id' => $produto->getId()
            ]);
        } else {
            $sql = "INSERT INTO produtos (nome, preco) VALUES (:nome, :preco)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':nome' => $produto->getNome(),
                ':preco' => $produto->getPreco()
            ]);
            if ($result) {
                $produto->setId((int)$this->conn->lastInsertId());
            }
            return $result;
        }
    }

    // Buscar por id
    public function buscarPorId(int $id): ?Produto
    {
        $stmt = $this->conn->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Produto($row['nome'], (float)$row['preco'], (int)$row['id'], $row['criado_em']);
    }

    // Listar todos
    public function listarTodos(): array
    {
        $stmt = $this->conn->query("SELECT * FROM produtos ORDER BY criado_em DESC");
        $produtos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $produtos[] = new Produto($row['nome'], (float)$row['preco'], (int)$row['id'], $row['criado_em']);
        }
        return $produtos;
    }

    // Deletar
    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM produtos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
