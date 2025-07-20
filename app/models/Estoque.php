<?php
class Estoque
{
    // Identificador único do registro de estoque (pode ser nulo para novo registro)
    private ?int $id;

    // ID do produto ao qual este estoque está associado (obrigatório e positivo)
    private int $produtoId;

    // Variação do produto (ex: cor, tamanho), pode ser nulo caso não haja variações
    private ?string $variacao;

    // Quantidade disponível em estoque (não pode ser negativa)
    private int $quantidade;

    /**
     * Construtor do objeto Estoque.
     * Inicializa as propriedades com validações.
     * 
     * @param int $produtoId ID do produto (deve ser positivo)
     * @param string|null $variacao Variação do produto, pode ser null
     * @param int $quantidade Quantidade em estoque (>= 0)
     * @param int|null $id ID do registro, null se ainda não salvo
     */
    public function __construct(int $produtoId, ?string $variacao, int $quantidade, ?int $id = null)
    {
        $this->setProdutoId($produtoId);
        $this->setVariacao($variacao);
        $this->setQuantidade($quantidade);
        $this->id = $id;
    }

    /**
     * Retorna o ID do registro de estoque, ou null se ainda não salvo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do registro de estoque.
     * 
     * @param int $id ID positivo
     * @throws InvalidArgumentException se o ID for inválido
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }
        $this->id = $id;
    }

    /**
     * Retorna o ID do produto associado.
     */
    public function getProdutoId(): int
    {
        return $this->produtoId;
    }

    /**
     * Define o ID do produto associado ao estoque.
     * 
     * @param int $produtoId ID positivo do produto
     * @throws InvalidArgumentException se o ID do produto for inválido
     */
    public function setProdutoId(int $produtoId): void
    {
        if ($produtoId <= 0) {
            throw new InvalidArgumentException("Produto ID inválido");
        }
        $this->produtoId = $produtoId;
    }

    /**
     * Retorna a variação do produto (pode ser null).
     */
    public function getVariacao(): ?string
    {
        return $this->variacao;
    }

    /**
     * Define a variação do produto.
     * Remove espaços em branco e converte string vazia para null.
     * 
     * @param string|null $variacao Variação ou null
     */
    public function setVariacao(?string $variacao): void
    {
        if ($variacao !== null) {
            $variacao = trim($variacao);
            if ($variacao === '') {
                // Se a string estiver vazia após trim, considera null
                $variacao = null; 
            }
        }
        $this->variacao = $variacao;
    }

    /**
     * Retorna a quantidade em estoque.
     */
    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /**
     * Define a quantidade em estoque.
     * 
     * @param int $quantidade Valor >= 0
     * @throws InvalidArgumentException se a quantidade for negativa
     */
    public function setQuantidade(int $quantidade): void
    {
        if ($quantidade < 0) {
            throw new InvalidArgumentException("Quantidade não pode ser negativa");
        }
        $this->quantidade = $quantidade;
    }

    /**
     * Converte o objeto Estoque para array associativo,
     * útil para exportar dados ou retorno JSON.
     * 
     * @return array Dados do estoque
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'produto_id' => $this->produtoId,
            'variacao' => $this->variacao,
            'quantidade' => $this->quantidade,
        ];
    }
}
