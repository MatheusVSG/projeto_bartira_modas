<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$id = $_POST['id'];
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];

$sql = "UPDATE clientes SET nome=?, cpf=?, email=?, data_atualizacao=NOW() WHERE id=?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    registrar_log(
        $conn,
        'Erro ao preparar atualização de cliente',
        mysqli_error($conn),
        $_SERVER['REQUEST_URI'],
        'controller/cliente/cliente_controller.php'
    );
    die('Erro ao preparar a consulta.');
}

$stmt->bind_param("sssi", $nome, $cpf, $email, $id);

if ($stmt->execute()) {
    header("Location: ../../view/cliente/listar_clientes.php?id=$id&atualizado=1");
    exit;
} else {
    registrar_log(
        $conn,
        'Erro ao atualizar cliente',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/cliente/atualizar_cliente.php'
    );
    echo "Erro ao atualizar cliente: " . $stmt->error;
}
