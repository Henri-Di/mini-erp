<?php
class Cupom
{
    // Identificador único do cupom (pode ser nulo para cupons ainda não salvos)
    private ?int $id;

    // Código do cupom utilizado para aplicar desconto
    private string $codigo;

    // Valor do desconto (pode ser percentual ou valor fixo, conforme implementação)
    private float $desconto;

    // Valor mínimo do subtotal do carrinho para que o cupom seja válido
    private float $minimoSubtotal;

    // Data de validade do cupom no formato YYYY-MM-DD
    private string $validade;

    /**
     * Construtor da classe Cupom.
     * Recebe os dados essenciais para criação ou atualização de um cupom.
     *
     * @param string $codigo Código do cupom (não pode ser vazio)
     * @param float $desconto Valor do desconto (não negativo)
     * @param float $minimoSubtotal Valor mínimo do subtotal para uso do cupom (não negativo)
     * @param string $validade Data de validade no formato 'YYYY-MM-DD'
     * @param int|null $id ID opcional, usado para cupons já existentes
     */
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

    /**
     * Retorna o ID do cupom ou null se ainda não salvo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Define o ID do cupom, valida para garantir que seja positivo.
     *
     * @throws InvalidArgumentException se o ID for inválido (<=0)
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }
        $this->id = $id;
    }

    /**
     * Retorna o código do cupom.
     */
    public function getCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * Define o código do cupom, garantindo que não seja vazio após trim.
     *
     * @throws InvalidArgumentException se o código estiver vazio
     */
    public function setCodigo(string $codigo): void
    {
        $codigo = trim($codigo);
        if (empty($codigo)) {
            throw new InvalidArgumentException("Código não pode ser vazio");
        }
        $this->codigo = $codigo;
    }

    /**
     * Retorna o valor do desconto do cupom.
     */
    public function getDesconto(): float
    {
        return $this->desconto;
    }

    /**
     * Define o valor do desconto, que não pode ser negativo.
     *
     * @throws InvalidArgumentException se o desconto for negativo
     */
    public function setDesconto(float $desconto): void
    {
        if ($desconto < 0) {
            throw new InvalidArgumentException("Desconto não pode ser negativo");
        }
        $this->desconto = $desconto;
    }

    /**
     * Retorna o valor mínimo do subtotal para o cupom ser válido.
     */
    public function getMinimoSubtotal(): float
    {
        return $this->minimoSubtotal;
    }

    /**
     * Define o valor mínimo do subtotal para o cupom ser válido.
     * Não pode ser negativo.
     *
     * @throws InvalidArgumentException se o valor mínimo for negativo
     */
    public function setMinimoSubtotal(float $minimoSubtotal): void
    {
        if ($minimoSubtotal < 0) {
            throw new InvalidArgumentException("Subtotal mínimo não pode ser negativo");
        }
        $this->minimoSubtotal = $minimoSubtotal;
    }

    /**
     * Retorna a data de validade do cupom.
     */
    public function getValidade(): string
    {
        return $this->validade;
    }

    /**
     * Define a data de validade do cupom.
     * Aqui você pode implementar validação de formato ou se a data é válida.
     *
     * @param string $validade Data no formato 'YYYY-MM-DD'
     */
    public function setValidade(string $validade): void
    {
        // Sugestão: validar formato e data real (opcional)
        $this->validade = $validade;
    }

    /**
     * Verifica se o cupom ainda está válido com base na data atual.
     *
     * @return bool Retorna true se a validade for hoje ou futura, false se expirado.
     */
    public function estaValido(): bool
    {
        return ($this->validade >= date('Y-m-d'));
    }

    /**
     * Converte o objeto Cupom para um array associativo,
     * útil para respostas JSON ou outras manipulações.
     *
     * @return array Dados do cupom em formato array
     */
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
