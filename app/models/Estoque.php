<?php
class Estoque
{
    private ?int $id;
    private int $produtoId;
    private ?string $variacao;
    private int $quantidade;

    public function __construct(int $produtoId, ?string $variacao, int $quantidade, ?int $id = null)
    {
        $this->setProdutoId($produtoId);
        $this->setVariacao($variacao);
        $this->setQuantidade($quantidade);
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

    public function getProdutoId(): int
    {
        return $this->produtoId;
    }

    public function setProdutoId(int $produtoId): void
    {
        if ($produtoId <= 0) {
            throw new InvalidArgumentException("Produto ID inválido");
        }
        $this->produtoId = $produtoId;
    }

    public function getVariacao(): ?string
    {
        return $this->variacao;
    }

    public function setVariacao(?string $variacao): void
    {
        if ($variacao !== null) {
            $variacao = trim($variacao);
            if ($variacao === '') {
                $variacao = null; // normalize vazio para null
            }
        }
        $this->variacao = $variacao;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): void
    {
        if ($quantidade < 0) {
            throw new InvalidArgumentException("Quantidade não pode ser negativa");
        }
        $this->quantidade = $quantidade;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'produtoId' => $this->produtoId,
            'variacao' => $this->variacao,
            'quantidade' => $this->quantidade,
        ];
    }
}
