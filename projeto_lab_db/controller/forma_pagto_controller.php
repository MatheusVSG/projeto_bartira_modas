<?php
include('../connection.php');
include('logger.controller.php');

if (isset($_POST['cadastrar_pagto'])) {
    $descricao = $_POST['descricao'];
    $query = "INSERT INTO forma_pagto (descricao) VALUES ('$descricao')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../view/forma_pagto/listar_forma_pagto.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao cadastrar forma de pagamento',
            mysqli_error($conn),
            $_SERVER['REQUEST_URI'],
            'controller/forma_pagto_controller.php'
        );
        echo "Erro ao cadastrar: " . mysqli_error($conn);
    }
}


if (isset($_POST['excluir_pagto'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM forma_pagto WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: ../view/forma_pagto/listar_forma_pagto.php");
        exit;
    } else {
        registrar_log(
            $conn,
            'Erro ao excluir forma de pagamento',
            mysqli_error($conn),
            $_SERVER['REQUEST_URI'],
            'controller/forma_pagto_controller.php'
        );
        echo "Erro ao cadastrar: " . mysqli_error($conn);
    }
}
