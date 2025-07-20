<?php
require_once '../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação do banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mini_erp DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE mini_erp");

    // Criação das tabelas
    $sql = "
        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS estoque (
            id INT AUTO_INCREMENT PRIMARY KEY,
            produto_id INT NOT NULL,
            variacao VARCHAR(255),
            quantidade INT NOT NULL DEFAULT 0,
            FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            valor_total DECIMAL(10,2) NOT NULL,
            frete DECIMAL(10,2) NOT NULL,
            endereco TEXT NOT NULL,
            cep VARCHAR(9) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'pendente',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS pedido_produto (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            produto_id INT NOT NULL,
            variacao VARCHAR(255),
            quantidade INT NOT NULL,
            preco_unitario DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
            FOREIGN KEY (produto_id) REFERENCES produtos(id)
        );

        CREATE TABLE IF NOT EXISTS cupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            desconto DECIMAL(10,2) NOT NULL,
            minimo_subtotal DECIMAL(10,2) DEFAULT 0,
            validade DATE NOT NULL
        );
    ";

    $pdo->exec($sql);

    echo "✅ Banco de dados e tabelas criadas com sucesso!";
} catch (PDOException $e) {
    die("Erro ao criar o banco: " . $e->getMessage());
}
