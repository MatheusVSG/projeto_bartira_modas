<?php
include_once '../connection.php';
include('logger.controller.php');

$id = $_GET['id'];

if (!mysqli_query($conn, "DELETE FROM vendedores WHERE id = $id")) {
    registrar_log(
        $conn,
        'Erro ao excluir vendedor',
        mysqli_error($conn),
        $_SERVER['REQUEST_URI'],
        'controller/excluir_vendedor.php'
    );
    echo "Erro ao excluir vendedor: " . mysqli_error($conn);
    exit;
}

header("Location: ../view/vendedor/cadastro_vendedor.php");
exit;
