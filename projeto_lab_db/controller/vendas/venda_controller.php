<?php
session_start();
include '../../connection.php';


if (!isset($_POST['cadastrar_venda'])) {
    header("Location: ../../view/venda/cadastrar_venda.php");
    exit();
}

$cliente_nome = $_POST['cliente_nome'];
$vendedor_id = $_POST['fk_vendedor_id'];
$forma_pagto_id = $_POST['fk_forma_pagto_id'];
$valor_total = $_POST['valor'];
$produtos = $_POST['produtos']; 

// Verifica se o cliente já existe
$query_check_cliente = "SELECT id FROM clientes WHERE nome = ?";
$stmt_check_cliente = $conn->prepare($query_check_cliente);
$stmt_check_cliente->bind_param("s", $cliente_nome);
$stmt_check_cliente->execute();
$result_check_cliente = $stmt_check_cliente->get_result();

if ($result_check_cliente->num_rows > 0) {
    // Cliente existe, usa o ID existente
    $cliente = $result_check_cliente->fetch_assoc();
    $cliente_id = $cliente['id'];
} else {
    // Cliente não existe, cadastra novo cliente
    $query_insert_cliente = "INSERT INTO clientes (nome) VALUES (?)";
    $stmt_insert_cliente = $conn->prepare($query_insert_cliente);
    $stmt_insert_cliente->bind_param("s", $cliente_nome);
    $stmt_insert_cliente->execute();
    $cliente_id = $conn->insert_id; // Obtém o ID do cliente recém-inserido
}
$stmt_check_cliente->close();

$queryVenda = "INSERT INTO vendas (fk_cliente_id, fk_vendedor_id, fk_forma_pagto_id, valor) 
               VALUES (?, ?, ?, ?)";
$stmtVenda = $conn->prepare($queryVenda);
$stmtVenda->bind_param("iiid", $cliente_id, $vendedor_id, $forma_pagto_id, $valor_total);
$stmtVenda->execute();

$venda_id = $conn->insert_id;


foreach ($produtos as $produto) {
    $produto_id = $produto['id'];
    $qtd = $produto['quantidade']; // Obter a quantidade do array de produtos
    
    // Inserir item na venda
    $queryItem = "INSERT INTO item_venda (fk_venda_id, fk_produto_id, qtd_vendida) 
                  VALUES (?, ?, ?)";
    $stmtItem = $conn->prepare($queryItem);
    $stmtItem->bind_param("iii", $venda_id, $produto_id, $qtd);
    $stmtItem->execute();

    // Atualizar o estoque
    $queryEstoque = "UPDATE estoque SET quantidade = quantidade - ? 
                     WHERE fk_produto_id = ? AND quantidade >= ?"; // Adicionado verificação de quantidade
    $stmtEstoque = $conn->prepare($queryEstoque);
    $stmtEstoque->bind_param("iii", $qtd, $produto_id, $qtd);
    $stmtEstoque->execute();
}


header("Location: ../../view/logout.php");
exit();
?>
