<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8"/>
  <title>Cadastro e Compra de Produtos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="/css/global.css" rel="stylesheet" />
  
  <style>
    /* Estilo básico e cores neutras para fundo e cards */
    body { background: #f4f6f9; min-height:100vh; }
    .card { border-radius:1rem; box-shadow:0 4px 25px rgba(0,0,0,0.05); }

    /* Título das seções com destaque em azul e uppercase */
    .form-section-title { 
      font-weight:700; color:#495057; border-bottom:2px solid #0d6efd;
      padding-bottom:0.25rem; margin-bottom:1.5rem; text-transform:uppercase; 
    }

    /* Informações do carrinho escondidas inicialmente e estilizadas */
    #carrinhoInfo { 
      display:none; border-top:3px solid #0d6efd; margin-top:1rem; padding:1rem;
      background:#fff; border-radius:0 0 1rem 1rem; transition:0.3s; 
    }

    /* Ícone azul para cabeçalhos dos cards */
    .card-header-icon { font-size:1.8rem; color:#0d6efd; margin-right:0.5rem; }

    /* Botões com estilo moderno, bordas arredondadas e efeitos hover */
    .btn-custom {
      border-radius: 50px;
      font-weight: 500;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      transition: all 0.2s ease-in-out;
    }
    .btn-custom:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
<div class="container py-5">

  <div class="row gy-4 app-root">
    <!-- ==========================
         SEÇÃO 1: Cadastro de Produto
         ========================== -->
    <section class="col-lg-4">
      <div class="card p-4">
        <header class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="bi bi-box-seam-fill card-header-icon"></i>
          <h4 class="mb-0">Cadastro de Produto</h4>
        </header>
        <form id="productForm">
          <!-- ID oculto para edição de produto -->
          <input type="hidden" id="produtoId" value="" />

          <!-- Campo para nome do produto -->
          <div class="mb-3">
            <label class="form-label" for="nome">Nome</label>
            <input type="text" id="nome" class="form-control" required/>
          </div>

          <!-- Campo para preço do produto -->
          <div class="mb-3">
            <label class="form-label" for="preco">Preço (R$)</label>
            <input type="number" id="preco" class="form-control" step="0.01" required/>
          </div>

          <!-- Título para variações/estoque -->
          <h6 class="form-section-title">Variações / Estoque</h6>

          <!-- Wrapper onde variações são adicionadas dinamicamente -->
          <div id="variationsWrapper" class="mb-3">
            <div class="row align-items-end mb-2">
              <div class="col-6">
                <input name="variacoes[0][nome]" class="form-control" placeholder="Variação" required/>
              </div>
              <div class="col-4">
                <input name="variacoes[0][estoque]" type="number" class="form-control" placeholder="Estoque" min="0" required/>
              </div>
              <div class="col-2">
                <!-- Botão para remover variação, inicialmente oculto -->
                <button type="button" class="btn btn-danger btn-sm btn-custom btn-remove-variation" style="display:none;">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Botão para adicionar nova variação -->
          <button type="button" class="btn btn-primary btn-custom mb-3" id="addVariationBtn">
            <i class="bi bi-plus-circle me-1"></i> Adicionar Variação
          </button>

          <!-- Botões para salvar produto e listar produtos -->
          <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success btn-custom">Salvar Produto</button>
            <button type="button" class="btn btn-secondary btn-custom" id="btnListarProdutos">
              <i class="bi bi-list-ul me-1"></i> Listar Produtos
            </button>
          </div>
        </form>
      </div>
    </section>

    <!-- ==========================
         SEÇÃO 2: Comprar Produto
         ========================== -->
    <section class="col-lg-4">
      <div class="card p-4 d-flex flex-column justify-content-between h-100">
        <header class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="bi bi-cart-plus-fill card-header-icon"></i>
          <h4 class="mb-0">Comprar Produto</h4>
        </header>

        <!-- Botão que abre modal para seleção de produtos -->
        <button type="button" class="btn btn-primary btn-custom mb-4" data-bs-toggle="modal" data-bs-target="#modalSelecionarProduto">
          <i class="bi bi-search me-1"></i> Selecionar Produtos
        </button>

        <!-- Informações do carrinho, inicialmente ocultas -->
        <div id="carrinhoInfo">
          <h5 class="fw-bold text-primary">Carrinho</h5>
          <ul id="cartList" class="list-group mb-3"></ul>

          <!-- Formulário para endereço de entrega -->
          <div class="mb-3">
            <label for="enderecoCompra" class="form-label">Endereço para Entrega</label>
            <input type="text" id="enderecoCompra" class="form-control" placeholder="Rua, número, complemento..." required/>
          </div>

          <!-- Campo para CEP -->
          <div class="mb-3">
            <label for="cepCompra" class="form-label">CEP</label>
            <input type="text" id="cepCompra" class="form-control" placeholder="CEP" required/>
          </div>

          <!-- Botão para finalizar pedido -->
          <button class="btn btn-success btn-custom w-100" id="btnFinalizarCompra">
            <i class="bi bi-bag-check-fill me-1"></i> Finalizar Pedido
          </button>
        </div>
      </div>
    </section>

    <!-- ==========================
         SEÇÃO 3: Gerenciar Cupons
         ========================== -->
    <section class="col-lg-4">
      <div class="card p-4 h-100">
        <header class="d-flex align-items-center mb-4 pb-2 border-bottom">
          <i class="bi bi-ticket-perforated-fill card-header-icon"></i>
          <h4 class="mb-0">Gerenciar Cupons</h4>
        </header>

        <!-- Formulário para cadastro/edição de cupom -->
        <form id="cupomForm" class="row g-3 mb-4">
          <input type="hidden" id="cupomId" value=""/>
          <div class="col-6">
            <input id="novoCodigo" placeholder="Código" class="form-control" required/>
          </div>
          <div class="col-3">
            <select id="novoTipo" class="form-select">
              <option value="percentual">%</option>
              <option value="fixo">R$</option>
            </select>
          </div>
          <div class="col-3">
            <input id="novoValor" type="number" placeholder="Valor" class="form-control" required min="0"/>
          </div>
          <div class="col-6">
            <input id="novoValidade" type="date" class="form-control" required/>
          </div>
          <div class="col-6">
            <input id="novoMinSubtotal" type="number" placeholder="Subtotal mínimo" class="form-control" required min="0"/>
          </div>
          <div class="col-12">
            <button class="btn btn-success btn-custom w-100" type="submit">
              <i class="bi bi-plus-circle-fill me-1"></i> Adicionar / Atualizar Cupom
            </button>
          </div>
        </form>

        <!-- Tabela para exibir cupons cadastrados -->
        <div class="table-responsive">
          <table class="table table-bordered table-sm text-center mb-0">
            <thead>
              <tr>
                <th>Código</th><th>Tipo</th><th>Valor</th><th>Validade</th><th>Mínimo</th><th>Ação</th>
              </tr>
            </thead>
            <tbody id="cupomTable"></tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- ==========================
     MODAL: Selecionar Produtos
     ========================== -->
<div class="modal fade" id="modalSelecionarProduto" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selecione Produtos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover mb-0">
          <thead><tr><th>Nome</th><th>Preço</th><th>+</th></tr></thead>
          <tbody id="productListModal"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  // ==========================
  // CONFIGURAÇÕES DAS URLs DA API
  // Ajuste as URLs conforme o backend configurado
  // ==========================
  const API_PRODUTO = '/api/produtos.php';
  const API_ESTOQUE = '/api/estoque.php';
  const API_CUPOM = '/api/cupons.php';
  const API_PEDIDO = '/api/pedidos.php';
  const API_PEDIDO_PRODUTO = '/api/pedido_produto.php';

  // ==========================
  // VARIÁVEL GLOBAL: CARRINHO
  // Mantém o estado local dos itens adicionados ao carrinho
  // ==========================
  const CART = [];

  // ==========================
  // FUNÇÃO: Manipulação do formulário de cadastro de produto
  // - Valida campos do produto e variações
  // - Envia requisições para salvar produto e estoque via API
  // - Reseta formulário e atualiza lista de produtos
  // ==========================
  document.getElementById('productForm').addEventListener('submit', async e => {
    e.preventDefault();

    const produtoId = document.getElementById('produtoId').value || null;
    const nome = document.getElementById('nome').value.trim();
    const preco = parseFloat(document.getElementById('preco').value);

    // Validação básica dos campos principais
    if (!nome || isNaN(preco)) {
      alert('Preencha nome e preço corretamente.');
      return;
    }

    // Coleta e valida variações (nome e estoque)
    const variacoesInputs = document.querySelectorAll('#variationsWrapper .row');
    const variacoes = [];
    for (let i = 0; i < variacoesInputs.length; i++) {
      const nomeVar = variacoesInputs[i].querySelector('input[name^="variacoes"][name$="[nome]"]').value.trim();
      const estoqueVar = parseInt(variacoesInputs[i].querySelector('input[name^="variacoes"][name$="[estoque]"]').value);
      if (!nomeVar || isNaN(estoqueVar) || estoqueVar < 0) {
        alert('Preencha corretamente todas as variações e estoques.');
        return;
      }
      variacoes.push({ nome: nomeVar, estoque: estoqueVar });
    }

    try {
      // Monta payload para salvar produto (POST ou PUT)
      let produtoPayload = { nome, preco };
      if (produtoId) produtoPayload.id = parseInt(produtoId);

      // Salva produto via API
      const resProduto = await fetch(API_PRODUTO, {
        method: produtoId ? 'PUT' : 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(produtoPayload)
      });
      if (!resProduto.ok) throw new Error('Erro ao salvar produto');
      const produtoData = await resProduto.json();

      // Salva variações/estoque associadas ao produto
      for (const variacao of variacoes) {
        const estoquePayload = {
          produto_id: produtoData.id,
          variacao: variacao.nome,
          quantidade: variacao.estoque
        };
        await fetch(API_ESTOQUE, {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(estoquePayload)
        });
      }

      alert(`Produto salvo com sucesso! ID: ${produtoData.id}`);

      // Reset formulário e variações para estado inicial
      e.target.reset();
      document.getElementById('produtoId').value = '';
      const wrapper = document.getElementById('variationsWrapper');
      wrapper.innerHTML = `
        <div class="row align-items-end mb-2">
          <div class="col-6"><input name="variacoes[0][nome]" class="form-control" placeholder="Variação" required/></div>
          <div class="col-4"><input name="variacoes[0][estoque]" type="number" class="form-control" placeholder="Estoque" min="0" required/></div>
          <div class="col-2">
            <button type="button" class="btn btn-danger btn-sm btn-custom btn-remove-variation" style="display:none;">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>`;
      atualizarBotoesRemover();

      // Atualiza modal de seleção de produtos para refletir mudanças
      carregarProdutosModal();

    } catch (err) {
      alert(err.message);
      console.error(err);
    }
  });

  // ==========================
  // ADICIONAR VARIAÇÃO DINÂMICA
  // Permite adicionar nova linha para variações e estoque
  // ==========================
  document.getElementById('addVariationBtn').addEventListener('click', () => {
    const wrapper = document.getElementById('variationsWrapper');
    const index = wrapper.children.length;
    const div = document.createElement('div');
    div.className = 'row align-items-end mb-2';
    div.innerHTML = `
      <div class="col-6"><input name="variacoes[${index}][nome]" class="form-control" placeholder="Variação" required/></div>
      <div class="col-4"><input name="variacoes[${index}][estoque]" type="number" class="form-control" placeholder="Estoque" min="0" required/></div>
      <div class="col-2">
        <button type="button" class="btn btn-danger btn-sm btn-custom btn-remove-variation">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    `;
    wrapper.appendChild(div);
    atualizarBotoesRemover();
  });

  // ==========================
  // REMOVER VARIAÇÃO DINÂMICA (delegado)
  // Remove linha de variação ao clicar no botão de remoção
  // ==========================
  document.getElementById('variationsWrapper').addEventListener('click', e => {
    if (e.target.closest('.btn-remove-variation')) {
      e.target.closest('.row').remove();
      atualizarBotoesRemover();
    }
  });

  // ==========================
  // ATUALIZA VISIBILIDADE DOS BOTÕES DE REMOVER VARIAÇÃO
  // Garante que o botão só apareça se houver mais de uma variação
  // ==========================
  function atualizarBotoesRemover() {
    const rows = document.querySelectorAll('#variationsWrapper .row');
    rows.forEach((row) => {
      const btn = row.querySelector('.btn-remove-variation');
      btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
    });
  }
  atualizarBotoesRemover();

  // ==========================
  // CARREGAR LISTA DE PRODUTOS PARA MODAL
  // Busca produtos do backend e exibe no modal para seleção
  // ==========================
  async function carregarProdutosModal() {
    try {
      const res = await fetch(API_PRODUTO);
      if (!res.ok) throw new Error('Falha ao carregar produtos');
      const produtos = await res.json();

      const modalList = document.getElementById('productListModal');
      modalList.innerHTML = '';

      produtos.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${p.nome}</td>
          <td>R$ ${parseFloat(p.preco).toFixed(2)}</td>
          <td><button class="btn btn-sm btn-success btn-custom"><i class="bi bi-plus-lg"></i></button></td>
        `;
        tr.querySelector('button').addEventListener('click', () => {
          adicionarAoCarrinho(p);
          // Fecha modal após adicionar produto
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalSelecionarProduto'));
          modal.hide();
        });
        modalList.appendChild(tr);
      });
    } catch (err) {
      alert('Erro ao carregar produtos.');
      console.error(err);
    }
  }
  document.getElementById('btnListarProdutos').addEventListener('click', carregarProdutosModal);

  // ==========================
  // GERENCIAMENTO DO CARRINHO
  // Funções para adicionar itens, renderizar lista e remover itens
  // ==========================
  function adicionarAoCarrinho(produto) {
    // Verifica se produto já existe no carrinho para incrementar quantidade
    const idx = CART.findIndex(p => p.id === produto.id);
    if (idx >= 0) {
      CART[idx].quantidade++;
    } else {
      CART.push({ ...produto, quantidade: 1 });
    }
    renderCart();
  }

  function renderCart() {
    const carrinhoInfo = document.getElementById('carrinhoInfo');
    const ul = document.getElementById('cartList');

    if (CART.length === 0) {
      carrinhoInfo.style.display = 'none';
      ul.innerHTML = '';
      return;
    }

    carrinhoInfo.style.display = 'block';
    ul.innerHTML = '';

    CART.forEach((p, i) => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `
        ${p.nome} x${p.quantidade} <span>R$ ${(p.preco * p.quantidade).toFixed(2)}</span>
        <button class="btn btn-sm btn-danger btn-custom"><i class="bi bi-x-lg"></i></button>
      `;
      li.querySelector('button').addEventListener('click', () => {
        if (p.quantidade > 1) {
          p.quantidade--;
        } else {
          CART.splice(i, 1);
        }
        renderCart();
      });
      ul.appendChild(li);
    });
  }

  // ==========================
  // FINALIZAR COMPRA
  // Envia pedido e seus itens para backend via API, limpa carrinho e formulários
  // ==========================
  document.getElementById('btnFinalizarCompra').addEventListener('click', async () => {
    if (!CART.length) return alert('Carrinho vazio.');

    const endereco = document.getElementById('enderecoCompra').value.trim();
    const cep = document.getElementById('cepCompra').value.trim();

    if (!endereco || !cep) {
      alert('Informe o endereço e CEP para entrega.');
      return;
    }

    try {
      // Cria o pedido com dados básicos
      const pedidoPayload = { endereco, cep, data_pedido: new Date().toISOString() };
      const resPedido = await fetch(API_PEDIDO, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(pedidoPayload)
      });
      if (!resPedido.ok) throw new Error('Erro ao criar pedido');
      const pedidoData = await resPedido.json();

      // Associa produtos e quantidades ao pedido criado
      for (const item of CART) {
        const pedidoProdutoPayload = {
          pedido_id: pedidoData.id,
          produto_id: item.id,
          quantidade: item.quantidade,
          preco_unitario: item.preco
        };
        await fetch(API_PEDIDO_PRODUTO, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(pedidoProdutoPayload)
        });
      }

      alert('Pedido finalizado com sucesso!');

      // Limpa estado e formulários
      CART.length = 0;
      renderCart();
      document.getElementById('enderecoCompra').value = '';
      document.getElementById('cepCompra').value = '';

    } catch (err) {
      alert(err.message);
      console.error(err);
    }
  });

  // ==========================
  // GERENCIAMENTO DE CUPONS
  // Funções para listar, salvar e remover cupons via API
  // ==========================
  async function listarCupons() {
    try {
      const res = await fetch(API_CUPOM);
      if (!res.ok) throw new Error('Erro ao listar cupons');
      const cupons = await res.json();

      const tb = document.getElementById('cupomTable');
      tb.innerHTML = '';

      cupons.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${c.codigo}</td>
          <td>${c.tipo === 'percentual' ? '%' : 'R$'}</td>
          <td>${c.valor}</td>
          <td>${c.validade}</td>
          <td>${c.min_subtotal}</td>
          <td>
            <button class="btn btn-danger btn-sm btn-custom btn-remover-cupom">
              <i class="bi bi-x-lg"></i>
            </button>
          </td>
        `;
        tr.querySelector('.btn-remover-cupom').addEventListener('click', () => removerCupom(c.id));
        tb.appendChild(tr);
      });
    } catch (err) {
      alert('Erro ao carregar cupons');
      console.error(err);
    }
  }

  async function salvarCupom(cupom) {
    const method = cupom.id ? 'PUT' : 'POST';
    const url = cupom.id ? `${API_CUPOM}?id=${cupom.id}` : API_CUPOM;
    const res = await fetch(url, {
      method,
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(cupom)
    });
    if (!res.ok) throw new Error('Erro ao salvar cupom');
    return await res.json();
  }

  async function removerCupom(id) {
    if (!confirm('Confirma remoção do cupom?')) return;
    const res = await fetch(`${API_CUPOM}?id=${id}`, { method: 'DELETE' });
    if (!res.ok) {
      alert('Erro ao remover cupom');
      return;
    }
    alert('Cupom removido');
    listarCupons();
  }

  // Formulário de cupom - salvar/atualizar cupom via API
  document.getElementById('cupomForm').addEventListener('submit', async e => {
    e.preventDefault();

    try {
      const cupomId = document.getElementById('cupomId').value || null;
      const cupom = {
        id: cupomId ? parseInt(cupomId) : undefined,
        codigo: document.getElementById('novoCodigo').value.trim(),
        tipo: document.getElementById('novoTipo').value,
        valor: parseFloat(document.getElementById('novoValor').value),
        validade: document.getElementById('novoValidade').value,
        min_subtotal: parseFloat(document.getElementById('novoMinSubtotal').value)
      };

      // Validação dos campos
      if (!cupom.codigo || isNaN(cupom.valor) || !cupom.validade || isNaN(cupom.min_subtotal)) {
        alert('Preencha todos os campos corretamente.');
        return;
      }

      await salvarCupom(cupom);
      alert('Cupom salvo com sucesso!');

      // Reset formulário e listar novamente os cupons
      e.target.reset();
      document.getElementById('cupomId').value = '';
      listarCupons();

    } catch (err) {
      alert(err.message);
      console.error(err);
    }
  });

  // Inicialização: listar cupons ao carregar página
  listarCupons();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
