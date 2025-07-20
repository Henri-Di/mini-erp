<?php
require_once __DIR__.'/../models/Pedido.php';

class PedidoController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function salvar(Pedido $pedido): bool
    {
        if ($pedido->getId()) {
            $sql = "UPDATE pedidos SET valor_total = :valorTotal, frete = :frete, endereco = :endereco, cep = :cep, status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':valorTotal' => $pedido->getValorTotal(),
                ':frete' => $pedido->getFrete(),
                ':endereco' => $pedido->getEndereco(),
                ':cep' => $pedido->getCep(),
                ':status' => $pedido->getStatus(),
                ':id' => $pedido->getId()
            ]);
        } else {
            $sql = "INSERT INTO pedidos (valor_total, frete, endereco, cep, status) VALUES (:valorTotal, :frete, :endereco, :cep, :status)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':valorTotal' => $pedido->getValorTotal(),
                ':frete' => $pedido->getFrete(),
                ':endereco' => $pedido->getEndereco(),
                ':cep' => $pedido->getCep(),
                ':status' => $pedido->getStatus()
            ]);
            if ($result) {
                $pedido->setId((int)$this->conn->lastInsertId());
            }
            return $result;
        }
    }

    public function buscarPorId(int $id): ?Pedido
    {
        $stmt = $this->conn->prepare("SELECT * FROM pedidos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Pedido(
            (float)$row['valor_total'],
            (float)$row['frete'],
            $row['endereco'],
            $row['cep'],
            $row['status'],
            (int)$row['id'],
            $row['criado_em']
        );
    }

    public function listarTodos(): array
    {
        $stmt = $this->conn->query("SELECT * FROM pedidos ORDER BY criado_em DESC");
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = new Pedido(
                (float)$row['valor_total'],
                (float)$row['frete'],
                $row['endereco'],
                $row['cep'],
                $row['status'],
                (int)$row['id'],
                $row['criado_em']
            );
        }
        return $pedidos;
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM pedidos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
