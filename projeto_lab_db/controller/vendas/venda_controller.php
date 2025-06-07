<?php
session_start();
include '../../connection.php';

if (!isset($_POST['cadastrar_venda'])) {
    header("Location: ../../view/venda/cadastrar_venda.php");
    exit();
}

$cliente_id = $_POST['fk_cliente_id'];
$vendedor_id = $_POST['fk_vendedor_id'];
$forma_pagto_id = $_POST['fk_forma_pagto_id'];
$valor_total = $_POST['valor'];
$produtos = $_POST['produtos']; 

try {
    $conn->begin_transaction();

    // Verificar estoque antes de iniciar a venda
    foreach ($produtos as $produto) {
        list($produto_id, $tamanho) = explode('-', $produto['id']);
        $qtd = intval($produto['quantidade']);
        
        // Verificar se a quantidade é válida
        if ($qtd <= 0) {
            throw new Exception("Quantidade inválida para o produto.");
        }

        // Verificar se há estoque suficiente
        $query_check_estoque = "SELECT quantidade FROM estoque 
                               WHERE fk_produto_id = ? AND tamanho = ?";
        $stmt_check_estoque = $conn->prepare($query_check_estoque);
        $stmt_check_estoque->bind_param("is", $produto_id, $tamanho);
        $stmt_check_estoque->execute();
        $result_check_estoque = $stmt_check_estoque->get_result();
        
        if ($result_check_estoque->num_rows === 0) {
            throw new Exception("Produto não encontrado no estoque.");
        }

        $estoque = $result_check_estoque->fetch_assoc();
        if ($estoque['quantidade'] < $qtd) {
            throw new Exception("Estoque insuficiente. Disponível: " . $estoque['quantidade'] . " unidades.");
        }
    }

    // Inserir a venda
    $queryVenda = "INSERT INTO vendas (fk_cliente_id, fk_vendedor_id, fk_forma_pagto_id, valor) 
                   VALUES (?, ?, ?, ?)";
    $stmtVenda = $conn->prepare($queryVenda);
    $stmtVenda->bind_param("iiid", $cliente_id, $vendedor_id, $forma_pagto_id, $valor_total);
    
    if (!$stmtVenda->execute()) {
        throw new Exception("Erro ao registrar a venda: " . $stmtVenda->error);
    }

    $venda_id = $conn->insert_id;

    foreach ($produtos as $produto) {
        list($produto_id, $tamanho) = explode('-', $produto['id']);
        $qtd = intval($produto['quantidade']);
        
        // Inserir item na venda
        $queryItem = "INSERT INTO item_venda (fk_venda_id, fk_produto_id, qtd_vendida) 
                      VALUES (?, ?, ?)";
        $stmtItem = $conn->prepare($queryItem);
        $stmtItem->bind_param("iii", $venda_id, $produto_id, $qtd);
        
        if (!$stmtItem->execute()) {
            throw new Exception("Erro ao registrar item da venda: " . $stmtItem->error);
        }

        // Atualizar o estoque
        $queryEstoque = "UPDATE estoque 
                        SET quantidade = quantidade - ? 
                        WHERE fk_produto_id = ? AND tamanho = ?";
        $stmtEstoque = $conn->prepare($queryEstoque);
        $stmtEstoque->bind_param("iis", $qtd, $produto_id, $tamanho);
        
        if (!$stmtEstoque->execute()) {
            throw new Exception("Erro ao atualizar estoque: " . $stmtEstoque->error);
        }
    }

    $conn->commit();
    $_SESSION['success_message'] = 'Venda registrada com sucesso!';
    header("Location: ../../view/venda/cadastrar_venda.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../../view/venda/cadastrar_venda.php");
    exit();
}
?>
