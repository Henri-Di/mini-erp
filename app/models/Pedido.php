<?php
class Pedido
{
    // Identificador único do pedido (pode ser nulo se ainda não salvo)
    private ?int $id;

    // Valor total do pedido, incluindo produtos e frete, deve ser >= 0
    private float $valorTotal;

    // Valor do frete, deve ser >= 0
    private float $frete;

    // Endereço completo para entrega, não pode ser vazio
    private string $endereco;

    // CEP do endereço, não pode ser vazio (validação de formato pode ser adicionada)
    private string $cep;

    // Status atual do pedido: pendente, cancelado ou concluído
    private string $status;

    // Data e hora de criação do pedido, formato string (ex: '2023-07-20 10:00:00'), pode ser nulo
    private ?string $criadoEm;

    /**
     * Construtor para inicializar o objeto Pedido.
     * Aplica validações nos valores obrigatórios.
     * 
     * @param float $valorTotal Valor total do pedido (>= 0)
     * @param float $frete Valor do frete (>= 0)
     * @param string $endereco Endereço para entrega (não vazio)
     * @param string $cep CEP do endereço (não vazio)
     * @param string $status Status do pedido (padrão 'pendente')
     * @param int|null $id ID do pedido, nulo se novo registro
     * @param string|null $criadoEm Data de criação, pode ser nulo
     */
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

    /**
     * Retorna o ID do pedido ou null se não salvo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do pedido.
     * 
     * @param int $id Deve ser positivo
     * @throws InvalidArgumentException Se ID inválido
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }
        $this->id = $id;
    }

    /**
     * Retorna o valor total do pedido.
     */
    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    /**
     * Define o valor total do pedido.
     * 
     * @param float $valorTotal Deve ser >= 0
     * @throws InvalidArgumentException Se valor negativo
     */
    public function setValorTotal(float $valorTotal): void
    {
        if ($valorTotal < 0) {
            throw new InvalidArgumentException("Valor total não pode ser negativo");
        }
        $this->valorTotal = $valorTotal;
    }

    /**
     * Retorna o valor do frete.
     */
    public function getFrete(): float
    {
        return $this->frete;
    }

    /**
     * Define o valor do frete.
     * 
     * @param float $frete Deve ser >= 0
     * @throws InvalidArgumentException Se valor negativo
     */
    public function setFrete(float $frete): void
    {
        if ($frete < 0) {
            throw new InvalidArgumentException("Frete não pode ser negativo");
        }
        $this->frete = $frete;
    }

    /**
     * Retorna o endereço de entrega.
     */
    public function getEndereco(): string
    {
        return $this->endereco;
    }

    /**
     * Define o endereço de entrega.
     * Remove espaços em branco e valida que não está vazio.
     * 
     * @param string $endereco Não pode ser vazio
     * @throws InvalidArgumentException Se vazio
     */
    public function setEndereco(string $endereco): void
    {
        $endereco = trim($endereco);
        if (empty($endereco)) {
            throw new InvalidArgumentException("Endereço não pode ser vazio");
        }
        $this->endereco = $endereco;
    }

    /**
     * Retorna o CEP do endereço.
     */
    public function getCep(): string
    {
        return $this->cep;
    }

    /**
     * Define o CEP do endereço.
     * Remove espaços e valida que não está vazio.
     * Pode ser adicionada validação de formato aqui.
     * 
     * @param string $cep Não pode ser vazio
     * @throws InvalidArgumentException Se vazio
     */
    public function setCep(string $cep): void
    {
        $cep = trim($cep);
        if (empty($cep)) {
            throw new InvalidArgumentException("CEP não pode ser vazio");
        }
        $this->cep = $cep;
    }

    /**
     * Retorna o status atual do pedido.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Define o status do pedido.
     * Valida se é um dos valores aceitos.
     * 
     * @param string $status Deve ser 'pendente', 'cancelado' ou 'concluido'
     * @throws InvalidArgumentException Se status inválido
     */
    public function setStatus(string $status): void
    {
        $status = trim(strtolower($status));
        $valoresValidos = ['pendente', 'cancelado', 'concluido'];
        if (!in_array($status, $valoresValidos, true)) {
            throw new InvalidArgumentException("Status inválido");
        }
        $this->status = $status;
    }

    /**
     * Retorna a data e hora da criação do pedido.
     */
    public function getCriadoEm(): ?string
    {
        return $this->criadoEm;
    }

    /**
     * Define a data e hora da criação do pedido.
     * 
     * @param string $criadoEm Data no formato esperado pelo sistema
     */
    public function setCriadoEm(string $criadoEm): void
    {
        $this->criadoEm = $criadoEm;
    }

    /**
     * Converte o objeto Pedido para um array associativo,
     * útil para exportação JSON ou integração com APIs.
     * 
     * @return array Dados do pedido
     */
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
