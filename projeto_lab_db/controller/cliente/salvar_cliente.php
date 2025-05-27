<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$logradouro = $_POST['logradouro'];
$numero = $_POST['numero'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$sexo = $_POST['sexo'];

$sql = "INSERT INTO clientes 
(nome, cpf, email, telefone, logradouro, numero, bairro, cidade, estado, sexo)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $sexo);

if ($stmt->execute()) {
    header("Location: ../../view/cliente/cadastro_cliente.php?sucesso=1");
    exit;
} else {
    registrar_log(
        $conn,
        'Erro ao salvar cliente',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/cliente/salvar_cliente.php'
    );
    echo "Erro ao salvar cliente: " . $stmt->error;
}
