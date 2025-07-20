\# Mini ERP - Controle de Pedidos, Produtos, Cupons e Estoque ## Índice 

- [Descrição do Projeto](#descrição-do-projeto)   
- [Tecnologias Utilizadas](#tecnologias-utilizadas)   
- [Banco de Dados](#banco-de-dados)   
  - [Estrutura das Tabelas](#estrutura-das-tabelas)   
  - [Script SQL para criação do banco](#script-sql-para-criação-do-banco)   
- [Arquitetura do Sistema](#arquitetura-do-sistema)   
  - [Padrão MVC](#padrão-mvc)   
- [Funcionalidades](#funcionalidades)   
  - [Cadastro de Produtos](#cadastro-de-produtos)   
  - [Controle de Estoque](#controle-de-estoque)   
  - [Cadastro e Aplicação de Cupons](#cadastro-e-aplicação-de-cupons)   
  - [Carrinho de Compras](#carrinho-de-compras)   
  - [Cálculo do Frete](#cálculo-do-frete)   
  - [Validação de CEP via ViaCEP](#validação-de-cep-via-viacep)   
  - [Finalização do Pedido e Envio de E-mail](#finalização-do-pedido-e-envio-de-email)   
  - [Webhook para Atualização de Status do Pedido](#webhook-para-atualização-de-status-do-

pedido)   

- [Regras de Negócio e Validações](#regras-de-negócio-e-validações)   
- [Boas Práticas de Desenvolvimento](#boas-práticas-de-desenvolvimento)   
- [Instruções para Execução](#instruções-para-execução)   
- [Considerações Finais](#considerações-finais)   

\--- 

\## Descrição do Projeto 

Mini ERP desenvolvido para o controle simplificado de Pedidos, Produtos, Cupons e Estoque. A aplicação permite cadastro e atualização de produtos, variações e estoque, controle do carrinho de compras com cálculo dinâmico de frete e aplicação de cupons com regras de validade e valores mínimos. Inclui integração com API ViaCEP para validação de endereço via CEP, envio automático de e-mail ao finalizar pedido e webhook para atualização e cancelamento de pedidos. 

\--- 

\## Tecnologias Utilizadas 

- \*\*Backend:\*\* PHP Puro (Sem frameworks)   
- \*\*Frontend:\*\* HTML5, Bootstrap 5 (CSS e JS)   
- \*\*Banco de Dados:\*\* MySQL   
- \*\*Integrações:\*\*   
  - ViaCEP API para validação de CEP   
  - Biblioteca PHPMailer para envio de e-mails   
  - Webhook REST para atualização de pedidos   

\--- 

\## Banco de Dados 

\### Estrutura das Tabelas 

| Tabela     | Descrição                                       | Principais Campos                                             | 

|------------|------------------------------------------------|------------------------------------------------------------- -| 

| `produtos` | Armazena os produtos e suas variações          | id, nome, preco, variacoes (JSON), data\_criacao, data\_atualizacao | 

| `estoque`  | Controle de estoque por produto e variação     | id, produto\_id (FK), variacao, quantidade                     | 

| `pedidos`  | Registra os pedidos realizados                  | id, cliente\_nome, cliente\_email, cliente\_cep, status, subtotal, frete, cupom\_id, data\_pedido | 

| `cupons`   | Cupons de desconto com validade e regras       | id, codigo, desconto\_percentual, validade\_inicio, validade\_fim, valor\_minimo | 

\--- 

\### Script SQL para criação do banco 

\```sql 

CREATE DATABASE mini\_erp CHARACTER SET utf8mb4 COLLATE utf8mb4\_unicode\_ci; 

USE mini\_erp; 

CREATE TABLE produtos ( 

`    `id INT AUTO\_INCREMENT PRIMARY KEY,     nome VARCHAR(255) NOT NULL, 

`    `preco DECIMAL(10,2) NOT NULL, 

`    `variacoes JSON DEFAULT NULL, 

data\_criacao TIMESTAMP DEFAULT CURRENT\_TIMESTAMP, 

`    `data\_atualizacao TIMESTAMP DEFAULT CURRENT\_TIMESTAMP ON UPDATE CURRENT\_TIMESTAMP 

); 

CREATE TABLE estoque ( 

`    `id INT AUTO\_INCREMENT PRIMARY KEY, 

`    `produto\_id INT NOT NULL, 

`    `variacao VARCHAR(255) DEFAULT NULL, 

`    `quantidade INT DEFAULT 0, 

`    `FOREIGN KEY (produto\_id) REFERENCES produtos(id) ON DELETE CASCADE ); 

CREATE TABLE cupons ( 

`    `id INT AUTO\_INCREMENT PRIMARY KEY,     codigo VARCHAR(50) NOT NULL UNIQUE, 

`    `desconto\_percentual INT NOT NULL, 

`    `validade\_inicio DATE NOT NULL, 

`    `validade\_fim DATE NOT NULL, 

`    `valor\_minimo DECIMAL(10,2) NOT NULL ); 

CREATE TABLE pedidos ( 

`    `id INT AUTO\_INCREMENT PRIMARY KEY, 

`    `cliente\_nome VARCHAR(255) NOT NULL, 

`    `cliente\_email VARCHAR(255) NOT NULL, 

`    `cliente\_cep VARCHAR(20) NOT NULL, 

`    `status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',     subtotal DECIMAL(10,2) NOT NULL, 

`    `frete DECIMAL(10,2) NOT NULL, 

`    `cupom\_id INT DEFAULT NULL, 

`    `data\_pedido TIMESTAMP DEFAULT CURRENT\_TIMESTAMP, 

`    `FOREIGN KEY (cupom\_id) REFERENCES cupons(id) 

); 

Arquitetura do Sistema 

Padrão MVC 

Model: Responsável pela manipulação de dados, acesso e persistência no banco MySQL. Inclui classes para Produto, Estoque, Pedido e Cupom. 

View: Páginas HTML utilizando Bootstrap para a interface, formulários, listagens e modais. 

Controller: Controladores que recebem as requisições, tratam a lógica, chamam os Models e retornam Views ou respostas JSON (para AJAX). 

A separação clara facilita manutenção, testes e extensão futura. 

Funcionalidades Cadastro de Produtos 

Permite inserir e editar produtos com nome, preço e variações (opcionalmente armazenadas em JSON). 

Variações podem ser adicionadas dinamicamente via interface (ex: tamanho, cor). Cada variação possui seu próprio controle de estoque. 

Controle de Estoque 

Estoque controlado por produto e variação. 

Permite atualização manual do estoque. 

Em processos de compra, o estoque é conferido e atualizado automaticamente. Cadastro e Aplicação de Cupons 

Interface para criação e gerenciamento de cupons com: código, percentual de desconto, validade e valor mínimo para aplicar. 

Cupons aplicados são validados contra as regras no momento da finalização do pedido. 

Carrinho de Compras Implementado via sessão PHP. 

Permite adicionar produtos (com variações) ao carrinho. 

Controla quantidade, subtotal, cupons aplicados e cálculo de frete. 

Cálculo do Frete 

Regras baseadas no subtotal do pedido: 

Subtotal entre R$52,00 e R$166,59 → frete R$15,00 

Subtotal maior que R$200,00 → frete grátis Outros valores → frete R$20,00 

Validação de CEP via ViaCEP 

Na finalização do pedido, o CEP é validado através da API pública https://viacep.com.br/. 

Caso CEP inválido, impede a conclusão do pedido, solicitando correção. 

Finalização do Pedido e Envio de E-mail 

Após confirmação dos dados, pedido é salvo no banco com status "pendente". 

Um e-mail automático é enviado ao cliente com detalhes do pedido, endereço e status. PHPMailer é utilizado para o envio de e-mails. 

Webhook para Atualização de Status do Pedido 

Endpoint REST que recebe JSON com id e status do pedido. 

Se status for cancelado, o pedido é removido do banco. 

Para outros status, atualiza o status do pedido correspondente. 

Regras de Negócio e Validações 

Validação de formulário no frontend (Bootstrap + JS) e backend (PHP). 

Controle de estoque evita venda de produtos indisponíveis. 

Cupons só podem ser usados dentro da validade e se o subtotal mínimo for alcançado. Atualização do estoque ocorre apenas após confirmação do pedido. 

Preço e variações dos produtos são consistentes com o estoque para evitar inconsistências. CEP inválido bloqueia finalização do pedido para garantir entrega correta. 

Boas Práticas de Desenvolvimento 

Código organizado em pastas MVC (models/, views/, controllers/). 

Uso de prepared statements para evitar SQL Injection. 

Sanitização e validação dos dados do usuário em todas as entradas. Uso de sessões para manter estado do carrinho. 

Comentários e nomenclaturas claras para fácil entendimento. Tratamento de erros com mensagens amigáveis ao usuário. Modularização do código para fácil manutenção e escalabilidade. 

Instruções para Execução Configurar Banco: 

Importar o script SQL fornecido em um servidor MySQL. 

Ajustar credenciais no arquivo config.php (host, usuário, senha, banco). Configurar Servidor PHP: 

Usar Apache/Nginx com PHP 7.4+ ou superior. 

Definir document\_root para a pasta public/. 

Instalar Dependências: 

Se usar PHPMailer, instalar via composer ou manualmente. Acessar Sistema: 

Via browser, abrir URL da aplicação. 

Navegar até tela de produtos para cadastro. 

Testar o fluxo de compra e finalização. 

Considerações Finais 

O sistema é simples e modular, adequado para pequenas operações. 

Pode ser facilmente expandido com funcionalidades adicionais como relatórios, dashboard, autenticação. 

A separação MVC facilita futuras migrações para frameworks. 

O uso do PHP puro demonstra domínio da linguagem e atenção às boas práticas. Integração com APIs externas e envio de e-mails agregam valor real ao sistema. 
