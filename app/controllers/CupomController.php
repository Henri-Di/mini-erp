<?php
require_once __DIR__ . '/../models/Cupom.php';
require_once __DIR__ . '/../core/Database.php';

class CupomController
{
    // Instância do wrapper Database para executar queries com PDO
    private Database $db;

    // Construtor recebe uma instância de Database para injeção de dependência
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Salva um cupom no banco de dados.
     * Se o cupom já tem ID, realiza UPDATE.
     * Caso contrário, realiza INSERT e atualiza o ID do objeto cupom.
     *
     * @param Cupom $cupom
     * @return bool Retorna true se a operação foi bem-sucedida, false caso contrário.
     */
    public function salvar(Cupom $cupom): bool
    {
        if ($cupom->getId()) {
            // Atualização de cupom existente pelo ID
            $this->db->query("UPDATE cupons SET desconto = :desconto, minimo_subtotal = :minimoSubtotal, validade = :validade WHERE id = :id");
            $this->db->bind(':desconto', $cupom->getDesconto());
            $this->db->bind(':minimoSubtotal', $cupom->getMinimoSubtotal());
            $this->db->bind(':validade', $cupom->getValidade());
            $this->db->bind(':id', $cupom->getId());
            return $this->db->execute();
        } else {
            // Inserção de novo cupom
            $this->db->query("INSERT INTO cupons (codigo, desconto, minimo_subtotal, validade) VALUES (:codigo, :desconto, :minimoSubtotal, :validade)");
            $this->db->bind(':codigo', $cupom->getCodigo());
            $this->db->bind(':desconto', $cupom->getDesconto());
            $this->db->bind(':minimoSubtotal', $cupom->getMinimoSubtotal());
            $this->db->bind(':validade', $cupom->getValidade());

            $result = $this->db->execute();

            // Se inserção foi bem-sucedida, atualiza o ID do objeto cupom
            if ($result) {
                $cupom->setId((int)$this->db->lastInsertId());
            }

            return $result;
        }
    }

    /**
     * Busca um cupom pelo seu código único.
     *
     * @param string $codigo Código do cupom para busca.
     * @return Cupom|null Retorna o objeto Cupom se encontrado, ou null caso não exista.
     */
    public function buscarPorCodigo(string $codigo): ?Cupom
    {
        $this->db->query("SELECT * FROM cupons WHERE codigo = :codigo");
        $this->db->bind(':codigo', $codigo);
        $row = $this->db->single();

        if (!$row) return null;

        // Cria e retorna um objeto Cupom populado com os dados do banco
        return new Cupom(
            $row['codigo'],
            (float)$row['desconto'],
            (float)$row['minimo_subtotal'],
            $row['validade'],
            (int)$row['id']
        );
    }

    /**
     * Lista todos os cupons cadastrados, ordenados por validade decrescente.
     *
     * @return Cupom[] Array de objetos Cupom.
     */
    public function listarTodos(): array
    {
        $this->db->query("SELECT * FROM cupons ORDER BY validade DESC");
        $resultados = $this->db->resultSet();

        $cupons = [];
        foreach ($resultados as $row) {
            $cupons[] = new Cupom(
                $row['codigo'],
                (float)$row['desconto'],
                (float)$row['minimo_subtotal'],
                $row['validade'],
                (int)$row['id']
            );
        }

        return $cupons;
    }

    /**
     * Deleta um cupom pelo seu ID.
     *
     * @param int $id ID do cupom a ser deletado.
     * @return bool Retorna true se deletado com sucesso, false caso contrário.
     */
    public function deletar(int $id): bool
    {
        $this->db->query("DELETE FROM cupons WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
