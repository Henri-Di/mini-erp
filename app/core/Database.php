<?php

class Database
{
    private string $host = DB_HOST;
    private string $dbname = DB_NAME;
    private string $user = DB_USER;
    private string $pass = DB_PASS;

    private ?PDO $dbh = null;       // PDO handle
    private ?PDOStatement $stmt = null;  // PDOStatement

    /**
     * Construtor: cria conexão PDO
     *
     * @throws PDOException Se falhar a conexão
     */
    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Opcional: registrar em log antes de relançar
            throw $e; // Permite tratamento de erro externo
        }
    }

    /**
     * Prepara uma query SQL para execução
     *
     * @param string $sql Query SQL
     * @return void
     * @throws Exception Se PDO não estiver inicializado
     */
    public function query(string $sql): void
    {
        if (!$this->dbh) {
            throw new Exception("Conexão com o banco não inicializada");
        }

        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * Faz bind de um parâmetro na query preparada
     *
     * @param string|int $param Nome ou posição do parâmetro
     * @param mixed $value Valor a ser vinculado
     * @param int|null $type Tipo PDO (ex: PDO::PARAM_INT)
     * @return void
     * @throws Exception Se stmt não estiver preparado
     */
    public function bind($param, $value, ?int $type = null): void
    {
        if (!$this->stmt) {
            throw new Exception("Nenhuma query preparada para bind");
        }

        if (is_null($type)) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Executa a query preparada
     *
     * @return bool Sucesso da execução
     * @throws Exception Se stmt não estiver preparado
     */
    public function execute(): bool
    {
        if (!$this->stmt) {
            throw new Exception("Nenhuma query preparada para executar");
        }

        return $this->stmt->execute();
    }

    /**
     * Retorna todos os registros do resultado da query
     *
     * @return array Lista de resultados
     * @throws Exception Se exec falhar ou stmt não preparado
     */
    public function resultSet(): array
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Retorna um único registro do resultado da query
     *
     * @return array|false Registro ou false se não encontrado
     * @throws Exception Se exec falhar ou stmt não preparado
     */
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Retorna número de linhas afetadas na última operação
     *
     * @return int Número de linhas afetadas
     * @throws Exception Se stmt não preparado
     */
    public function rowCount(): int
    {
        if (!$this->stmt) {
            throw new Exception("Nenhuma query preparada para rowCount");
        }
        return $this->stmt->rowCount();
    }

    /**
     * Retorna o ID do último registro inserido
     *
     * @return string ID do último insert
     * @throws Exception Se conexão não inicializada
     */
    public function lastInsertId(): string
    {
        if (!$this->dbh) {
            throw new Exception("Conexão não inicializada para lastInsertId");
        }
        return $this->dbh->lastInsertId();
    }

    /**
     * Inicia uma transação
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->dbh->beginTransaction();
    }

    /**
     * Comita uma transação
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->dbh->commit();
    }

    /**
     * Faz rollback de uma transação
     *
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->dbh->rollBack();
    }

    /**
     * Executa query direta com parâmetros opcionais (pronto para uso rápido)
     *
     * @param string $sql SQL para executar
     * @param array $params Parâmetros para bind (opcional)
     * @return bool Sucesso da execução
     * @throws Exception
     */
    public function executeQuery(string $sql, array $params = []): bool
    {
        $this->query($sql);
        foreach ($params as $param => $value) {
            // Bind pelo índice ou nome
            $this->bind(is_int($param) ? $param + 1 : $param, $value);
        }
        return $this->execute();
    }
}
