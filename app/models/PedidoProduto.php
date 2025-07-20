<?php
class PedidoProduto
{
    // Identificador único do item do pedido (pode ser nulo se ainda não salvo)
    private ?int $id;

    // ID do pedido ao qual este item pertence, deve ser positivo
    private int $pedidoId;

    // ID do produto relacionado a este item, deve ser positivo
    private int $produtoId;

    // Variação do produto (ex: cor, tamanho), pode ser nulo ou string vazia tratada como nulo
    private ?string $variacao;

    // Quantidade do produto neste item do pedido, deve ser maior que zero
    private int $quantidade;

    // Preço unitário do produto no momento do pedido, não pode ser negativo
    private float $precoUnitario;

    /**
     * Construtor para inicializar o item do pedido com seus atributos essenciais.
     * Aplica validações via setters para garantir integridade dos dados.
     * 
     * @param int $pedidoId ID do pedido ao qual o item pertence
     * @param int $produtoId ID do produto comprado
     * @param string|null $variacao Variação do produto, pode ser nulo
     * @param int $quantidade Quantidade solicitada (> 0)
     * @param float $precoUnitario Preço unitário (>= 0)
     * @param int|null $id ID do item, nulo se ainda não salvo no banco
     * 
     * @throws InvalidArgumentException para valores inválidos
     */
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

    /**
     * Retorna o ID do item do pedido, ou null se não salvo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do item do pedido.
     * 
     * @param int $id Deve ser positivo
     * @throws InvalidArgumentException Se ID inválido (<= 0)
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }
        $this->id = $id;
    }

    /**
     * Retorna o ID do pedido ao qual o item pertence.
     */
    public function getPedidoId(): int
    {
        return $this->pedidoId;
    }

    /**
     * Define o ID do pedido.
     * 
     * @param int $pedidoId Deve ser positivo
     * @throws InvalidArgumentException Se inválido (<= 0)
     */
    public function setPedidoId(int $pedidoId): void
    {
        if ($pedidoId <= 0) {
            throw new InvalidArgumentException("Pedido ID inválido");
        }
        $this->pedidoId = $pedidoId;
    }

    /**
     * Retorna o ID do produto comprado.
     */
    public function getProdutoId(): int
    {
        return $this->produtoId;
    }

    /**
     * Define o ID do produto.
     * 
     * @param int $produtoId Deve ser positivo
     * @throws InvalidArgumentException Se inválido (<= 0)
     */
    public function setProdutoId(int $produtoId): void
    {
        if ($produtoId <= 0) {
            throw new InvalidArgumentException("Produto ID inválido");
        }
        $this->produtoId = $produtoId;
    }

    /**
     * Retorna a variação do produto (ex: cor, tamanho).
     * Pode ser nulo.
     */
    public function getVariacao(): ?string
    {
        return $this->variacao;
    }

    /**
     * Define a variação do produto.
     * Trims espaços em branco e converte string vazia para null.
     * 
     * @param string|null $variacao
     */
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

    /**
     * Retorna a quantidade do produto no pedido.
     */
    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /**
     * Define a quantidade.
     * Deve ser maior que zero, pois pedido com 0 quantidade não faz sentido.
     * 
     * @param int $quantidade
     * @throws InvalidArgumentException Se quantidade <= 0
     */
    public function setQuantidade(int $quantidade): void
    {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("Quantidade deve ser maior que zero");
        }
        $this->quantidade = $quantidade;
    }

    /**
     * Retorna o preço unitário do produto no momento do pedido.
     */
    public function getPrecoUnitario(): float
    {
        return $this->precoUnitario;
    }

    /**
     * Define o preço unitário.
     * Não pode ser negativo.
     * 
     * @param float $precoUnitario
     * @throws InvalidArgumentException Se preço negativo
     */
    public function setPrecoUnitario(float $precoUnitario): void
    {
        if ($precoUnitario < 0) {
            throw new InvalidArgumentException("Preço unitário não pode ser negativo");
        }
        $this->precoUnitario = $precoUnitario;
    }

    /**
     * Converte o objeto para um array associativo.
     * Útil para serialização, resposta API, etc.
     * 
     * @return array Dados do item do pedido
     */
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
