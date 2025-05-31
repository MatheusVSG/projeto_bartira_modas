<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Acesso inválido. Requisição não permitida!';
    header("Location: ../../view/cliente/cadastro_cliente.php");
    exit;
}

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
$sexo = $_POST['sexo'] ?? '';

if (empty(trim($nome))) {
    $_SESSION['error_message'] = 'O nome não pode ficar em branco!';
    header("Location: ../../view/cliente/cadastro_cliente.php");
    exit;
}

try {
    $sql = "INSERT INTO clientes (nome, cpf, email, telefone, logradouro, numero, bairro, cidade, estado, sexo)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $sexo);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Cliente cadastrado com sucesso!';
        header("Location: ../../view/cliente/cadastro_cliente.php");
        exit;
    } else {
        throw new Exception('Erro na execução da consulta: ' . $stmt->error);
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao salvar cliente',
        $e->getMessage(), // Usa a mensagem do erro diretamente
        $_SERVER['REQUEST_URI'],
        'controller/cliente/salvar_cliente.php'
    );
    $_SESSION['error_message'] = 'Erro ao salvar cliente. Verifique os dados e tente novamente.';
    header("Location: ../../view/cliente/cadastro_cliente.php");
    exit;
}
