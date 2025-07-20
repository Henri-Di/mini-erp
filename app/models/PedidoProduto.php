<?php
class PedidoProduto
{
    private ?int $id;
    private int $pedidoId;
    private int $produtoId;
    private ?string $variacao;
    private int $quantidade;
    private float $precoUnitario;

    public function __construct(
        int $pedidoId,
        int $produtoId,
        ?string $variacao,
        int $quantidade,
        float $precoUnitario,
        ?int $id = null
    ) {
        $this->setPedidoId($pedidoId);
        $this->setProdutoId($produtoId);
        $this->setVariacao($variacao);
        $this->setQuantidade($quantidade);
        $this->setPrecoUnitario($precoUnitario);
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

    public function getPedidoId(): int
    {
        return $this->pedidoId;
    }
    public function setPedidoId(int $pedidoId): void
    {
        if ($pedidoId <= 0) {
            throw new InvalidArgumentException("Pedido ID inválido");
        }
        $this->pedidoId = $pedidoId;
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
                $variacao = null;
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
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("Quantidade deve ser maior que zero");
        }
        $this->quantidade = $quantidade;
    }

    public function getPrecoUnitario(): float
    {
        return $this->precoUnitario;
    }
    public function setPrecoUnitario(float $precoUnitario): void
    {
        if ($precoUnitario < 0) {
            throw new InvalidArgumentException("Preço unitário não pode ser negativo");
        }
        $this->precoUnitario = $precoUnitario;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pedidoId' => $this->pedidoId,
            'produtoId' => $this->produtoId,
            'variacao' => $this->variacao,
            'quantidade' => $this->quantidade,
            'precoUnitario' => $this->precoUnitario,
        ];
    }
}
