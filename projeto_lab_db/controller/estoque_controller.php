<?php
include('../connection.php');
include('logs/logger.controller.php');

if (isset($_POST['cadastrar_estoque'])) {
    $tamanho = $_POST['tamanho'];
    $fk_produto_id = $_POST['fk_produto_id'];
    $quantidade = $_POST['quantidade'];

    $query = "INSERT INTO estoque (tamanho, fk_produto_id, quantidade) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $tamanho, $fk_produto_id, $quantidade);

    if ($stmt->execute()) {
        header("Location: ../view/estoque/listar_estoque.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao cadastrar estoque',
            $stmt->error,
            $_SERVER['REQUEST_URI'],
            'controller/estoque_controller.php',
            'estoque'
        );
        echo "Erro ao cadastrar: " . $stmt->error;
    }
}

if (isset($_POST['remover_quantia_estoque'])) {
    $tamanho = $_POST['tamanho'];
    $fk_produto_id = $_POST['fk_produto_id'];
    $quantidade_remover = $_POST['quantidade_remover'];

    // Buscar a quantidade atual no estoque
    $query_atual = "SELECT quantidade FROM estoque WHERE tamanho = ? AND fk_produto_id = ?";
    $stmt_atual = $conn->prepare($query_atual);
    $stmt_atual->bind_param("si", $tamanho, $fk_produto_id);
    $stmt_atual->execute();
    $result_atual = $stmt_atual->get_result();
    $row_atual = $result_atual->fetch_assoc();
    $quantidade_atual = $row_atual['quantidade'] ?? 0;

    // Calcular nova quantidade
    $nova_quantidade = max(0, $quantidade_atual - $quantidade_remover);

    // Atualizar o estoque
    $query_update = "UPDATE estoque SET quantidade = ? WHERE tamanho = ? AND fk_produto_id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("isi", $nova_quantidade, $tamanho, $fk_produto_id);

    if ($stmt_update->execute()) {
        header("Location: ../view/estoque/listar_estoque.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao remover quantia do estoque',
            $stmt_update->error,
            $_SERVER['REQUEST_URI'],
            'controller/estoque_controller.php',
            'estoque'
        );
        echo "Erro ao remover quantia: " . $stmt_update->error;
    }
}

if (isset($_POST['excluir_estoque'])) {
    $tamanho = $_POST['tamanho'];
    $fk_produto_id = $_POST['fk_produto_id'];

    $query = "DELETE FROM estoque WHERE tamanho = ? AND fk_produto_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tamanho, $fk_produto_id);

    if ($stmt->execute()) {
        header("Location: ../view/estoque/listar_estoque.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao excluir estoque',
            $stmt->error,
            $_SERVER['REQUEST_URI'],
            'controller/estoque_controller.php',
            'estoque'
        );
        echo "Erro ao excluir: " . $stmt->error;
    }
}
