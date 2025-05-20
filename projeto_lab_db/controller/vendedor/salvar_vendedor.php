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
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$modificado_por = 1;

$stmt = $conn->prepare("INSERT INTO vendedores 
(nome, cpf, email, telefone, logradouro, numero, bairro, cidade, estado, sexo, senha, modificado_por) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssi", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $sexo, $senha, $modificado_por);

if ($stmt->execute()) {
    header("Location: ../../view/vendedor/cadastro_vendedor.php");
    exit;
} else {
    registrar_log(
        $conn,
        'Erro ao cadastrar vendedor',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/vendedor/salvar_vendedor.php'
    );
    echo "Erro ao cadastrar: " . $stmt->error;
}
