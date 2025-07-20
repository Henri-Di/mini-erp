<?php
class Cupom
{
    private ?int $id;
    private string $codigo;
    private float $desconto;
    private float $minimoSubtotal;
    private string $validade;

    public function __construct(
        string $codigo,
        float $desconto,
        float $minimoSubtotal,
        string $validade,
        ?int $id = null
    ) {
        $this->setCodigo($codigo);
        $this->setDesconto($desconto);
        $this->setMinimoSubtotal($minimoSubtotal);
        $this->setValidade($validade);
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }
        $this->id = $id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }
    public function setCodigo(string $codigo): void
    {
        $codigo = trim($codigo);
        if (empty($codigo)) {
            throw new InvalidArgumentException("Código não pode ser vazio");
        }
        $this->codigo = $codigo;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }
    public function setDesconto(float $desconto): void
    {
        if ($desconto < 0) {
            throw new InvalidArgumentException("Desconto não pode ser negativo");
        }
        $this->desconto = $desconto;
    }

    public function getMinimoSubtotal(): float
    {
        return $this->minimoSubtotal;
    }
    public function setMinimoSubtotal(float $minimoSubtotal): void
    {
        if ($minimoSubtotal < 0) {
            throw new InvalidArgumentException("Subtotal mínimo não pode ser negativo");
        }
        $this->minimoSubtotal = $minimoSubtotal;
    }

    public function getValidade(): string
    {
        return $this->validade;
    }
    public function setValidade(string $validade): void
    {
        // Pode adicionar validação de formato de data se quiser
        $this->validade = $validade;
    }

    public function estaValido(): bool
    {
        return ($this->validade >= date('Y-m-d'));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'desconto' => $this->desconto,
            'minimoSubtotal' => $this->minimoSubtotal,
            'validade' => $this->validade,
        ];
    }
}
