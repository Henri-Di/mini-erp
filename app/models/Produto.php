<?php
class Produto
{
    private ?int $id;
    private string $nome;
    private float $preco;
    private ?string $criadoEm;

    public function __construct(string $nome, float $preco, ?int $id = null, ?string $criadoEm = null)
    {
        $this->setNome($nome);
        $this->setPreco($preco);
        $this->id = $id;
        $this->criadoEm = $criadoEm;
    }

    // Getters e setters

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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $nome = trim($nome);
        if (empty($nome)) {
            throw new InvalidArgumentException("Nome não pode ser vazio");
        }
        $this->nome = $nome;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function setPreco(float $preco): void
    {
        if ($preco < 0) {
            throw new InvalidArgumentException("Preço não pode ser negativo");
        }
        $this->preco = $preco;
    }

    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(string $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    // Método útil para exportar dados em array (ex: JSON)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'preco' => $this->preco,
            'criadoEm' => $this->criadoEm,
        ];
    }
}
