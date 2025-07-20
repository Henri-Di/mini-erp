# Mini ERP - Controle de Pedidos, Produtos, Cupons e Estoque

---

## Índice

- [Descrição do Projeto](#descrição-do-projeto)  
- [Tecnologias Utilizadas](#tecnologias-utilizadas)  
- [Banco de Dados](#banco-de-dados)  
  - [Estrutura das Tabelas](#estrutura-das-tabelas)  
  - [Script SQL para Criação do Banco](#script-sql-para-criação-do-banco)  
- [Arquitetura do Sistema](#arquitetura-do-sistema)  
  - [Padrão MVC](#padrão-mvc)  
- [Funcionalidades](#funcionalidades)  
  - [Cadastro de Produtos](#cadastro-de-produtos)  
  - [Controle de Estoque](#controle-de-estoque)  
  - [Cadastro e Aplicação de Cupons](#cadastro-e-aplicação-de-cupons)  
  - [Carrinho de Compras](#carrinho-de-compras)  
  - [Cálculo do Frete](#cálculo-do-frete)  
  - [Validação de CEP via ViaCEP](#validação-de-cep-via-viacep)  
  - [Finalização do Pedido e Envio de E-mail](#finalização-do-pedido-e-envio-de-e-mail)  
  - [Webhook para Atualização de Status do Pedido](#webhook-para-atualização-de-status-do-pedido)  
- [Regras de Negócio e Validações](#regras-de-negócio-e-validações)  
- [Boas Práticas de Desenvolvimento](#boas-práticas-de-desenvolvimento)  
- [Instruções para Execução](#instruções-para-execução)  
- [Considerações Finais](#considerações-finais)  

---

## Descrição do Projeto

Este **Mini ERP** foi desenvolvido para oferecer um sistema simples e eficiente de controle de **Pedidos, Produtos, Cupons e Estoque**.

A aplicação permite o cadastro e gerenciamento de produtos com variações, controle detalhado do estoque, aplicação de cupons de desconto com regras específicas e um carrinho de compras com cálculo dinâmico de frete.

Além disso, integra-se à API pública ViaCEP para validação de CEPs e envia automaticamente e-mails de confirmação dos pedidos.

Um webhook REST permite atualização e cancelamento dos pedidos em tempo real.

---

## Tecnologias Utilizadas

| Camada           | Tecnologias                                                                                  |
| ---------------- | ------------------------------------------------------------------------------------------- |
| Backend          | PHP Puro (sem frameworks)                                                                   |
| Frontend         | HTML5, Bootstrap 5 (CSS e JavaScript)                                                      |
| Banco de Dados   | MySQL                                                                                      |
| Integrações      | - API ViaCEP para validação de CEP<br>- Biblioteca PHPMailer para envio de e-mails<br>- Webhook REST para atualização de status dos pedidos |

---

## Banco de Dados

### Estrutura das Tabelas

| Tabela    | Descrição                                  | Principais Campos                                              |
| --------- | ----------------------------------------- | ------------------------------------------------------------- |
| produtos  | Armazena os produtos e suas variações     | id, nome, preco, variacoes (JSON), timestamps                 |
| estoque   | Controle de estoque por produto e variação | id, produto_id (FK), variacao, quantidade                     |
| cupons    | Cupons de desconto com validade e regras  | id, codigo, desconto_percentual, validade_inicio, validade_fim, valor_minimo |
| pedidos   | Registra os pedidos realizados             | id, cliente_nome, cliente_email, cliente_cep, status, subtotal, frete, cupom_id, data_pedido |

---

### Script SQL para Criação do Banco

```sql
CREATE DATABASE mini_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mini_erp;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    variacoes JSON DEFAULT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao VARCHAR(255) DEFAULT NULL,
    quantidade INT DEFAULT 0,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    desconto_percentual INT NOT NULL,
    validade_inicio DATE NOT NULL,
    validade_fim DATE NOT NULL,
    valor_minimo DECIMAL(10,2) NOT NULL
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_cep VARCHAR(20) NOT NULL,
    status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL,
    cupom_id INT DEFAULT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id)
); 

```

# Arquitetura do Sistema

## Padrão MVC

- **Model:**  
  Responsável pela manipulação dos dados e acesso ao banco MySQL, contendo as classes para **Produto**, **Estoque**, **Pedido** e **Cupom**.

- **View:**  
  Páginas HTML estruturadas com Bootstrap para exibição de formulários, tabelas, listagens e modais.

- **Controller:**  
  Controladores que recebem as requisições do usuário, processam a lógica de negócio, interagem com os Models e retornam as Views ou respostas JSON para requisições AJAX.

> Essa separação facilita a manutenção, testes e extensões futuras do sistema.

---

# Funcionalidades

## Cadastro de Produtos

- Inserção e edição de produtos com nome, preço e variações opcionais (armazenadas em JSON).
- Interface dinâmica para adicionar múltiplas variações, como tamanho e cor, cada uma com controle individual de estoque.

## Controle de Estoque

- Controle detalhado por produto e variação.
- Atualização manual ou automática do estoque durante o processo de compra.

## Cadastro e Aplicação de Cupons

- Interface para criação e gerenciamento de cupons com código, percentual de desconto, período de validade e valor mínimo para aplicação.
- Validação dos cupons no momento da finalização do pedido.

## Carrinho de Compras

- Implementado usando sessões PHP para manter estado entre requisições.
- Adição de produtos e variações ao carrinho com controle de quantidade, subtotal, cupons aplicados e cálculo dinâmico do frete.

## Cálculo do Frete

| Subtotal do Pedido          | Valor do Frete  |
|----------------------------|-----------------|
| Entre R$ 52,00 e R$ 166,59 | R$ 15,00        |
| Acima de R$ 200,00         | Frete grátis    |
| Outros valores             | Frete padrão R$ 20,00 |

## Validação de CEP via ViaCEP

- Consulta automática à API pública ViaCEP para validar o CEP informado na finalização do pedido.
- Bloqueio da finalização caso o CEP seja inválido, solicitando correção.

## Finalização do Pedido e Envio de E-mail

- Salva o pedido no banco com status **"pendente"** após confirmação dos dados.
- Envia e-mail automático ao cliente com detalhes do pedido, endereço e status, utilizando **PHPMailer**.

## Webhook para Atualização de Status do Pedido

- Endpoint REST que recebe atualizações em JSON contendo o **id** e novo **status** do pedido.
- Permite cancelar pedidos (removendo do banco) ou atualizar status conforme necessidade.

---

# Regras de Negócio e Validações

- Validação rigorosa tanto no frontend (HTML, Bootstrap, JavaScript) quanto no backend (PHP).
- Controle de estoque para evitar vendas de produtos indisponíveis.
- Cupons só podem ser aplicados dentro do prazo de validade e respeitando valor mínimo de compra.
- Atualização do estoque ocorre somente após confirmação e finalização do pedido.
- Garantia de consistência entre preços, variações e estoque para evitar inconsistências.
- CEP inválido bloqueia finalização para garantir entrega correta.

---

# Boas Práticas de Desenvolvimento

- Organização clara do código em pastas seguindo o padrão MVC (`models/`, `views/`, `controllers/`).
- Uso de **prepared statements** para evitar ataques de SQL Injection.
- Sanitização e validação de todos os dados recebidos do usuário.
- Utilização de sessões para manter o estado do carrinho.
- Comentários explicativos e nomenclaturas claras para facilitar entendimento e manutenção.
- Tratamento de erros amigável ao usuário.
- Modularização para escalabilidade futura.

---

# Instruções para Execução

1. **Configurar Banco de Dados:**  
   - Importar o script SQL para criar o banco e as tabelas.  
   - Ajustar as credenciais no arquivo `config.php` (host, usuário, senha, nome do banco).

2. **Configurar Servidor PHP:**  
   - Utilizar Apache ou Nginx com PHP 7.4 ou superior.  
   - Definir a pasta `public/` como document root.

3. **Instalar Dependências:**  
   - Caso use PHPMailer, instalar via Composer ou manualmente.

4. **Acessar a Aplicação:**  
   - Abrir a URL no navegador configurada para o projeto.  
   - Navegar para o cadastro de produtos, controle de estoque e realizar testes de compra.

---

# Considerações Finais

Este sistema Mini ERP é simples e modular, ideal para pequenas operações comerciais que necessitam controle básico de pedidos e estoque.

Sua arquitetura MVC facilita futuras expansões, como a inclusão de dashboards, relatórios detalhados e autenticação de usuários.

O uso de PHP puro demonstra domínio técnico e atenção às boas práticas, enquanto a integração com APIs externas (ViaCEP e PHPMailer) agrega funcionalidades importantes para a experiência do usuário e operacional.

> **Sinta-se à vontade para customizar e expandir este projeto conforme as necessidades do seu negócio.**

---

*Desenvolvido com foco em simplicidade, escalabilidade e boas práticas de desenvolvimento.*
