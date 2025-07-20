<?php

class Produto
{
    // ID único do produto, pode ser nulo se ainda não salvo no banco
    private ?int $id;

    // Nome do produto, string não vazia
    private string $nome;

    // Preço do produto, deve ser zero ou positivo
    private float $preco;

    // Data e hora da criação do registro, formato 'Y-m-d H:i:s', pode ser nulo
    private ?string $criadoEm;

    /**
     * Construtor para criar instância do Produto.
     * Valida e define nome e preço obrigatórios.
     * ID e criadoEm são opcionais, usados quando o produto vem do banco.
     * 
     * @param string $nome Nome do produto (não vazio)
     * @param float $preco Preço do produto (>= 0)
     * @param int|null $id ID do produto (opcional)
     * @param string|null $criadoEm Data/hora criação (opcional)
     * 
     * @throws InvalidArgumentException para valores inválidos
     */
    public function __construct(string $nome, float $preco, ?int $id = null, ?string $criadoEm = null)
    {
        $this->setNome($nome);
        $this->setPreco($preco);
        $this->id = $id;
        $this->criadoEm = $criadoEm;
    }

    /**
     * Retorna o ID do produto, ou null se não salvo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do produto.
     * Deve ser maior que zero.
     * 
     * @param int $id
     * @throws InvalidArgumentException Se ID inválido
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido, deve ser maior que zero.");
        }
        $this->id = $id;
    }

    /**
     * Retorna o nome do produto.
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Define o nome do produto.
     * Remove espaços no início/fim e verifica que não seja vazio.
     * 
     * @param string $nome
     * @throws InvalidArgumentException Se nome vazio
     */
    public function setNome(string $nome): void
    {
        $nome = trim($nome);
        if ($nome === '') {
            throw new InvalidArgumentException("Nome não pode ser vazio.");
        }
        $this->nome = $nome;
    }

    /**
     * Retorna o preço do produto.
     */
    public function getPreco(): float
    {
        return $this->preco;
    }

    /**
     * Define o preço do produto.
     * Deve ser maior ou igual a zero.
     * 
     * @param float $preco
     * @throws InvalidArgumentException Se preço negativo
     */
    public function setPreco(float $preco): void
    {
        if ($preco < 0) {
            throw new InvalidArgumentException("Preço não pode ser negativo.");
        }
        $this->preco = $preco;
    }

    /**
     * Retorna a data/hora de criação do produto, formato 'Y-m-d H:i:s', ou null.
     */
    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    /**
     * Define a data/hora de criação.
     * Valida formato correto ou permite null.
     * 
     * @param string|null $criadoEm
     * @throws InvalidArgumentException Se formato inválido
     */
    public function setCriadoEm(?string $criadoEm): void
    {
        if ($criadoEm !== null && !$this->validarDataHora($criadoEm)) {
            throw new InvalidArgumentException("Formato de data inválido para 'criadoEm'. Use Y-m-d H:i:s");
        }
        $this->criadoEm = $criadoEm;
    }

    /**
     * Valida se a string passada está no formato 'Y-m-d H:i:s'.
     * 
     * @param string $dataHora
     * @return bool True se válido, false caso contrário
     */
    private function validarDataHora(string $dataHora): bool
    {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dataHora);
        return $dt !== false && $dt->format('Y-m-d H:i:s') === $dataHora;
    }

    /**
     * Exporta os dados do produto em formato de array associativo.
     * Útil para serialização JSON ou manipulação geral.
     * 
     * @return array Dados do produto
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'preco' => $this->preco,
            'criado_em' => $this->criadoEm,
        ];
    }
}
