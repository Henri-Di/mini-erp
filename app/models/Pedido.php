<?php
class Pedido
{
    private ?int $id;
    private float $valorTotal;
    private float $frete;
    private string $endereco;
    private string $cep;
    private string $status;
    private ?string $criadoEm;

    public function __construct(
        float $valorTotal,
        float $frete,
        string $endereco,
        string $cep,
        string $status = 'pendente',
        ?int $id = null,
        ?string $criadoEm = null
    ) {
        $this->setValorTotal($valorTotal);
        $this->setFrete($frete);
        $this->setEndereco($endereco);
        $this->setCep($cep);
        $this->setStatus($status);
        $this->id = $id;
        $this->criadoEm = $criadoEm;
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

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }
    public function setValorTotal(float $valorTotal): void
    {
        if ($valorTotal < 0) {
            throw new InvalidArgumentException("Valor total não pode ser negativo");
        }
        $this->valorTotal = $valorTotal;
    }

    public function getFrete(): float
    {
        return $this->frete;
    }
    public function setFrete(float $frete): void
    {
        if ($frete < 0) {
            throw new InvalidArgumentException("Frete não pode ser negativo");
        }
        $this->frete = $frete;
    }

    public function getEndereco(): string
    {
        return $this->endereco;
    }
    public function setEndereco(string $endereco): void
    {
        $endereco = trim($endereco);
        if (empty($endereco)) {
            throw new InvalidArgumentException("Endereço não pode ser vazio");
        }
        $this->endereco = $endereco;
    }

    public function getCep(): string
    {
        return $this->cep;
    }
    public function setCep(string $cep): void
    {
        $cep = trim($cep);
        if (empty($cep)) {
            throw new InvalidArgumentException("CEP não pode ser vazio");
        }
        // Você pode adicionar validação de formato de CEP aqui
        $this->cep = $cep;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): void
    {
        $status = trim(strtolower($status));
        $valoresValidos = ['pendente', 'cancelado', 'concluido'];
        if (!in_array($status, $valoresValidos, true)) {
            throw new InvalidArgumentException("Status inválido");
        }
        $this->status = $status;
    }

    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(string $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'valorTotal' => $this->valorTotal,
            'frete' => $this->frete,
            'endereco' => $this->endereco,
            'cep' => $this->cep,
            'status' => $this->status,
            'criadoEm' => $this->criadoEm,
        ];
    }
}
