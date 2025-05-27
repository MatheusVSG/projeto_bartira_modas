<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$id = $_GET['id'];

$sql = "DELETE FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    registrar_log(
        $conn,
        'Erro ao excluir cliente',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/cliente/excluir_cliente.php'
    );
    echo "Erro ao excluir cliente: " . $stmt->error;
    exit;
}

header("Location: ../../view/cliente/listar_clientes.php?excluido=1");
exit;
