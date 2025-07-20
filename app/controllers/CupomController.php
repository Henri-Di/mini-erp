<?php
require_once __DIR__.'/../models/Cupom.php';

class CupomController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function salvar(Cupom $cupom): bool
    {
        if ($cupom->getId()) {
            $sql = "UPDATE cupons SET desconto = :desconto, minimo_subtotal = :minimoSubtotal, validade = :validade WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':desconto' => $cupom->getDesconto(),
                ':minimoSubtotal' => $cupom->getMinimoSubtotal(),
                ':validade' => $cupom->getValidade(),
                ':id' => $cupom->getId()
            ]);
        } else {
            $sql = "INSERT INTO cupons (codigo, desconto, minimo_subtotal, validade) VALUES (:codigo, :desconto, :minimoSubtotal, :validade)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':codigo' => $cupom->getCodigo(),
                ':desconto' => $cupom->getDesconto(),
                ':minimoSubtotal' => $cupom->getMinimoSubtotal(),
                ':validade' => $cupom->getValidade()
            ]);
            if ($result) {
                $cupom->setId((int)$this->conn->lastInsertId());
            }
            return $result;
        }
    }

    public function buscarPorCodigo(string $codigo): ?Cupom
    {
        $stmt = $this->conn->prepare("SELECT * FROM cupons WHERE codigo = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Cupom(
            $row['codigo'],
            (float)$row['desconto'],
            (float)$row['minimo_subtotal'],
            $row['validade'],
            (int)$row['id']
        );
    }

    public function listarTodos(): array
    {
        $stmt = $this->conn->query("SELECT * FROM cupons ORDER BY validade DESC");
        $cupons = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cupons[] = new Cupom(
                $row['codigo'],
                (float)$row['desconto'],
                (float)$row['minimo_subtotal'],
                $row['validade'],
                (int)$row['id']
            );
        }
        return $cupons;
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM cupons WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
