<?php
include('../connection.php');
include('logger.controller.php');

if (isset($_POST['cadastrar_venda'])) {
    $cliente_id = $_POST['fk_cliente_id'];
    $vendedor_id = $_POST['fk_vendedor_id'];
    $forma_pagto_id = $_POST['fk_forma_pagto_id'];
    $valor = $_POST['valor'];

    $query = "INSERT INTO vendas (fk_cliente_id, fk_vendedor_id, fk_forma_pagto_id, valor)
              VALUES ('$cliente_id', '$vendedor_id', '$forma_pagto_id', '$valor')";

    if (mysqli_query($conn, $query)) {
        echo "Venda cadastrada com sucesso!";
    } else {
        registrar_log(
            $conn,
            'Erro ao cadastrar vendas',
            mysqli_error($conn),
            $_SERVER['REQUEST_URI'],
            'controller/vendas_controller.php'
        );
        echo "Erro ao cadastrar: " . mysqli_error($conn);
    }
}
