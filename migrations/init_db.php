<?php
// --------------------------------------------------
// Script de criação do banco de dados e tabelas
// Este script conecta ao servidor MySQL, cria o banco
// 'mini_erp' caso não exista, e em seguida cria todas
// as tabelas necessárias para o funcionamento do sistema.
// --------------------------------------------------

require_once '../config/config.php';

try {
    // Conecta ao servidor MySQL sem selecionar banco ainda
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);

    // Configura PDO para lançar exceções em erros para melhor controle
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria banco de dados com charset e collation recomendados para UTF-8
    $pdo->exec("
        CREATE DATABASE IF NOT EXISTS " . DB_NAME . " 
        DEFAULT CHARACTER SET utf8mb4 
        COLLATE utf8mb4_unicode_ci
    ");

    // Seleciona o banco criado ou existente
    $pdo->exec("USE " . DB_NAME);

    // Criação das tabelas com relacionamento entre elas
    $sql = "
        -- Tabela de produtos, armazena itens disponíveis para venda
        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Tabela de estoque, vincula produtos a suas variações e quantidade disponível
        CREATE TABLE IF NOT EXISTS estoque (
            id INT AUTO_INCREMENT PRIMARY KEY,
            produto_id INT NOT NULL,
            variacao VARCHAR(255),
            quantidade INT NOT NULL DEFAULT 0,
            CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) 
                REFERENCES produtos(id) ON DELETE CASCADE
        );

        -- Tabela de pedidos, armazena os pedidos realizados
        CREATE TABLE IF NOT EXISTS pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            valor_total DECIMAL(10,2) NOT NULL,
            frete DECIMAL(10,2) NOT NULL DEFAULT 0,
            endereco TEXT NOT NULL,
            cep VARCHAR(9) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'pendente',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Tabela intermediária entre pedidos e produtos,
        -- detalha quais produtos e variações estão em cada pedido
        CREATE TABLE IF NOT EXISTS pedido_produto (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            produto_id INT NOT NULL,
            variacao VARCHAR(255),
            quantidade INT NOT NULL,
            preco_unitario DECIMAL(10,2) NOT NULL,
            CONSTRAINT fk_pedido_produto_pedido FOREIGN KEY (pedido_id)
                REFERENCES pedidos(id) ON DELETE CASCADE,
            CONSTRAINT fk_pedido_produto_produto FOREIGN KEY (produto_id)
                REFERENCES produtos(id)
        );

        -- Tabela de cupons para descontos
        CREATE TABLE IF NOT EXISTS cupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            desconto DECIMAL(10,2) NOT NULL,
            minimo_subtotal DECIMAL(10,2) DEFAULT 0,
            validade DATE NOT NULL
        );
    ";

    // Executa a criação das tabelas no banco
    $pdo->exec($sql);

    echo "✅ Banco de dados e tabelas criadas com sucesso!";
} catch (PDOException $e) {
    // Captura erros e exibe mensagem detalhada
    die("❌ Erro ao criar o banco de dados ou tabelas: " . $e->getMessage());
}
