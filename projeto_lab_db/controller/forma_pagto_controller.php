<?php
include('../connection.php');
include('logs/logger.controller.php');

if (isset($_POST['cadastrar_pagto'])) {
    $descricao = $_POST['descricao'];

    $query = "INSERT INTO forma_pagto (descricao) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $descricao);

    if ($stmt->execute()) {
        header("Location: ../view/forma_pagto/listar_forma_pagto.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao cadastrar forma de pagamento',
            $stmt->error,
            $_SERVER['REQUEST_URI'],
            'controller/forma_pagto_controller.php',
            'forma_pagto'
        );
        echo "Erro ao cadastrar: " . $stmt->error;
    }
}

if (isset($_POST['editar_pagto'])) {
    $id = $_POST['id'];
    $descricao = $_POST['descricao'];

    $query = "UPDATE forma_pagto SET descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $descricao, $id);

    if ($stmt->execute()) {
        header("Location: ../view/forma_pagto/listar_forma_pagto.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao editar forma de pagamento',
            $stmt->error,
            $_SERVER['REQUEST_URI'],
            'controller/forma_pagto_controller.php',
            'forma_pagto'
        );
        echo "Erro ao editar: " . $stmt->error;
    }
}

if (isset($_POST['excluir_pagto'])) {
    $id = $_POST['id'];

    $query = "DELETE FROM forma_pagto WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../view/forma_pagto/listar_forma_pagto.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao excluir forma de pagamento',
            $stmt->error,
            $_SERVER['REQUEST_URI'],
            'controller/forma_pagto_controller.php',
            'forma_pagto'
        );
        echo "Erro ao excluir: " . $stmt->error;
    }
}
