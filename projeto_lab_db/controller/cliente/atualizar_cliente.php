<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Acesso inválido. Requisição não permitida!';
    header("Location: ../../view/cliente/listar_clientes.php");
    exit;
}

include_once '../../connection.php';
include('../logs/logger.controller.php');

$id = $_POST['id'];
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$logradouro = $_POST['logradouro'];
$numero = $_POST['numero'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];

if (empty(trim($nome))) {
    $_SESSION['error_message'] = 'O nome não pode ficar em branco!';
    header("Location: ../../view/cliente/editar_cliente.php?id=$id");
    exit;
}

try {
    $sql = "UPDATE clientes SET nome=?, cpf=?, email=?, telefone=?, logradouro=?, numero=?, bairro=?, cidade=?, estado=?, data_atualizacao=NOW() WHERE id=?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("sssssssssi", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Cliente atualizado com sucesso!';
        header("Location: ../../view/cliente/editar_cliente.php?id=$id");
        exit;
    } else {
        throw new Exception('Erro na execução da consulta: ' . $stmt->error);
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao atualizar cliente',
        $e->getMessage(),
        $_SERVER['REQUEST_URI'],
        'controller/cliente/atualizar_cliente.php'
    );

    $_SESSION['error_message'] = 'Erro ao atualizar cliente. Verifique os dados e tente novamente.';
    header("Location: ../../view/cliente/editar_cliente.php?id=$id");
    exit;
}
