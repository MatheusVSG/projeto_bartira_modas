<?php
include '../connection.php';
include('logger.controller.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fk_venda_id = $_POST['fk_venda_id'];
    $fk_produto_id = $_POST['fk_produto_id'];
    $qtd_vendida = $_POST['qtd_vendida'];

    if (!$fk_venda_id || !$fk_produto_id || !$qtd_vendida) {
        die("Todos os campos são obrigatórios.");
    }

    $sql = "INSERT INTO item_venda (fk_venda_id, fk_produto_id, qtd_vendida)
            VALUES ('$fk_venda_id', '$fk_produto_id', '$qtd_vendida')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../view/venda/item_venda.php?success=1");
    } else {
        registrar_log(
            $conn,
            'Erro ao inserir item venda',
            mysqli_error($conn),
            $_SERVER['REQUEST_URI'],
            'controller/item_venda_controller'
        );
        echo "Erro ao inserir: " . mysqli_error($conn);
    }
}
