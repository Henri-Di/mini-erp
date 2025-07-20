<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../core/Database.php';

class PedidoController
{
    // Instância da classe Database para execução das queries
    private Database $db;

    /**
     * Construtor que recebe a instância do Database via injeção de dependência.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Salva um objeto Pedido no banco.
     * Se o pedido possuir ID, realiza atualização (UPDATE).
     * Caso contrário, realiza inserção (INSERT) e atualiza o ID do objeto.
     *
     * @param Pedido $pedido
     * @return bool Retorna true se a operação foi bem-sucedida, false caso contrário.
     */
    public function salvar(Pedido $pedido): bool
    {
        if ($pedido->getId()) {
            // Atualiza um pedido existente com os dados fornecidos
            $this->db->query("UPDATE pedidos SET valor_total = :valorTotal, frete = :frete, endereco = :endereco, cep = :cep, status = :status WHERE id = :id");
            $this->db->bind(':valorTotal', $pedido->getValorTotal());
            $this->db->bind(':frete', $pedido->getFrete());
            $this->db->bind(':endereco', $pedido->getEndereco());
            $this->db->bind(':cep', $pedido->getCep());
            $this->db->bind(':status', $pedido->getStatus());
            $this->db->bind(':id', $pedido->getId());

            return $this->db->execute();
        } else {
            // Insere um novo pedido no banco, definindo a data de criação como NOW()
            $this->db->query("INSERT INTO pedidos (valor_total, frete, endereco, cep, status, criado_em) VALUES (:valorTotal, :frete, :endereco, :cep, :status, NOW())");
            $this->db->bind(':valorTotal', $pedido->getValorTotal());
            $this->db->bind(':frete', $pedido->getFrete());
            $this->db->bind(':endereco', $pedido->getEndereco());
            $this->db->bind(':cep', $pedido->getCep());
            $this->db->bind(':status', $pedido->getStatus());

            $result = $this->db->execute();

            // Atualiza o ID do objeto Pedido com o ID gerado na inserção
            if ($result) {
                $pedido->setId((int)$this->db->lastInsertId());
            }

            return $result;
        }
    }

    /**
     * Busca um pedido pelo seu ID.
     *
     * @param int $id ID do pedido
     * @return Pedido|null Retorna o objeto Pedido se encontrado, ou null caso não exista.
     */
    public function buscarPorId(int $id): ?Pedido
    {
        $this->db->query("SELECT * FROM pedidos WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        if (!$row) {
            // Retorna null caso não encontre nenhum pedido com o ID informado
            return null;
        }

        // Cria e retorna um objeto Pedido populado com os dados do banco
        return new Pedido(
            (float)$row['valor_total'],
            (float)$row['frete'],
            $row['endereco'],
            $row['cep'],
            $row['status'],
            (int)$row['id'],
            $row['criado_em']
        );
    }

    /**
     * Lista todos os pedidos existentes no banco, ordenados pela data de criação (mais recentes primeiro).
     *
     * @return Pedido[] Retorna um array de objetos Pedido.
     */
    public function listarTodos(): array
    {
        $this->db->query("SELECT * FROM pedidos ORDER BY criado_em DESC");
        $resultados = $this->db->resultSet();

        $pedidos = [];

        // Percorre os resultados e converte cada linha em um objeto Pedido
        foreach ($resultados as $row) {
            $pedidos[] = new Pedido(
                (float)$row['valor_total'],
                (float)$row['frete'],
                $row['endereco'],
                $row['cep'],
                $row['status'],
                (int)$row['id'],
                $row['criado_em']
            );
        }

        return $pedidos;
    }

    /**
     * Remove um pedido do banco pelo seu ID.
     *
     * @param int $id ID do pedido a ser deletado
     * @return bool Retorna true se deletado com sucesso, false caso contrário.
     */
    public function deletar(int $id): bool
    {
        $this->db->query("DELETE FROM pedidos WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }
}
