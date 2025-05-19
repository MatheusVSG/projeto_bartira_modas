<?php
session_start();
include '../connection.php';

// Verifica se o formulário foi enviado corretamente
if (!isset($_POST['cadastrar_venda'])) {
    header("Location: ../view/venda/cadastrar_venda.php");
    exit();
}

$cliente_id = $_POST['fk_cliente_id'];
$vendedor_id = $_POST['fk_vendedor_id'];
$forma_pagto_id = $_POST['fk_forma_pagto_id'];
$valor_total = $_POST['valor'];
$produtos = $_POST['produtos']; // array de produtos com id e preco

// 1. Inserir a venda
$queryVenda = "INSERT INTO vendas (fk_cliente_id, fk_vendedor_id, fk_forma_pagto_id, valor) 
               VALUES (?, ?, ?, ?)";
$stmtVenda = $conn->prepare($queryVenda);
$stmtVenda->bind_param("iiid", $cliente_id, $vendedor_id, $forma_pagto_id, $valor_total);
$stmtVenda->execute();

$venda_id = $conn->insert_id;

// 2. Inserir os produtos da venda
foreach ($produtos as $produto) {
    $produto_id = $produto['id'];
    $qtd = 1; // se for sempre 1 unidade, senão adicione um campo para isso
    $queryItem = "INSERT INTO item_venda (fk_venda_id, fk_produto_id, qtd_vendida) 
                  VALUES (?, ?, ?)";
    $stmtItem = $conn->prepare($queryItem);
    $stmtItem->bind_param("iii", $venda_id, $produto_id, $qtd);
    $stmtItem->execute();
}

// 3. Redirecionar para logout
// Após a venda ser finalizada, redireciona para logout.php
header("Location: ../view/logout.php");
exit();
?>
