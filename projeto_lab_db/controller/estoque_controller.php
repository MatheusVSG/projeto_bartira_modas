<?php
include('../connection.php');
include('logger.controller.php');

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
