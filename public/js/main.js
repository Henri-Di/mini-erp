/**
 * main.js
 * Arquivo principal JS para scripts globais da aplicação
 * Estrutura mínima, modular e robusta para futuras expansões
 * Autor: Seu Nome | Projeto Mini ERP
 * Data: 2025
 */

(() => {
  'use strict';

  /**
   * Função para inicialização do aplicativo.
   * Coloque aqui todo código que deve rodar no carregamento da página.
   */
  function init() {
    console.log('Aplicação iniciada com sucesso!');

    // Exemplo: Listener global para depuração de erros
    window.addEventListener('error', event => {
      console.error('Erro capturado:', event.message, 'Arquivo:', event.filename, 'Linha:', event.lineno);
      // Aqui você pode integrar com algum sistema de logs remoto
    });

    // Exemplo: Inicializar outras funcionalidades globais aqui
    // initFormValidation();
    // initAjaxSetup();
  }

  /**
   * Função para carregar scripts ou módulos adicionais dinamicamente.
   * Pode ser usada para modularizar conforme o projeto crescer.
   * @param {string} src Caminho do script a ser carregado
   * @returns {Promise<void>}
   */
  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = src;
      script.async = true;
      script.onload = () => resolve();
      script.onerror = () => reject(new Error(`Falha ao carregar script: ${src}`));
      document.head.appendChild(script);
    });
  }

  /**
   * Espera o DOM estar completamente carregado antes de iniciar a aplicação
   */
  document.addEventListener('DOMContentLoaded', () => {
    try {
      init();
    } catch (err) {
      console.error('Erro na inicialização:', err);
    }
  });

  // Exportar funções úteis para escopo global (se necessário)
  // window.app = { init, loadScript };
})();
