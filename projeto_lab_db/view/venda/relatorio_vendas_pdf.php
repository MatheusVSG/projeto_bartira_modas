<?php
// Ativa a exibição de todos os erros no navegador (útil para depuração)
ini_set('display_errors', 1); // Mostra erros durante a execução do script
ini_set('display_startup_errors', 1); // Mostra erros ocorridos no PHP na inicialização
error_reporting(E_ALL); // Exibe todos os tipos de erros, avisos e notices

// Inclui o arquivo do controlador responsável pelo relatório de vendas
require_once '../../controller/vendas/RelatorioVendasController.php';

// Cria uma instância do controlador
$controller = new RelatorioVendasController();

// Chama o método que gera e exibe o relatório de vendas em formato PDF
$controller->gerarRelatorioPDF();
