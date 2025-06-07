<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Acesso inválido. Requisição não permitida!';
    header("Location: ../../view/vendedor/listar_vendedores.php");
    exit;
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Permissão insuficiente!';
    header("Location: ../../login.php");
    exit;
}

include_once '../../connection.php';
include('../logs/logger.controller.php');

// Validação do ID
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] <= 0) {
    $_SESSION['error_message'] = 'Vendedor não identificado!';
    header("Location: ../../view/vendedor/listar_vendedores.php");
    exit;
}

$id = intval($_POST['id']);
$nome = trim($_POST['nome']);
$cpf = trim($_POST['cpf']);
$email = trim($_POST['email']);
$telefone = trim($_POST['telefone']);
$logradouro = trim($_POST['logradouro']);
$numero = trim($_POST['numero']);
$bairro = trim($_POST['bairro']);
$cidade = trim($_POST['cidade']);
$estado = trim($_POST['estado']);
$sexo = trim($_POST['sexo']);
$modificado_por = $_SESSION['usuario_id'];
$senha = password_hash(trim($_POST['senha']), PASSWORD_DEFAULT);

// Validações básicas
if (empty($nome)) {
    $_SESSION['error_message'] = 'O nome não pode ficar em branco!';
    header("Location: ../../view/vendedor/editar_vendedor.php?id=$id");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = 'E-mail inválido!';
    header("Location: ../../view/vendedor/editar_vendedor.php?id=$id");
    exit;
}

try {
    $sql = "UPDATE vendedores SET nome=?, cpf=?, email=?, telefone=?, logradouro=?, numero=?, bairro=?, cidade=?, estado=?, sexo=?, modificado_por=?, senha=?, data_atualizacao=NOW() WHERE id=?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro ao preparar a consulta: ' . $conn->error);
    }

    $stmt->bind_param("ssssssssssisi", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $sexo, $modificado_por, $senha, $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Vendedor atualizado com sucesso!';
        header("Location: ../../view/vendedor/editar_vendedor.php?id=$id");
        exit;
    } else {
        throw new Exception('Erro na execução da consulta: ' . $stmt->error);
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao atualizar vendedor',
        $e->getMessage(),
        $_SERVER['REQUEST_URI'],
        'controller/vendedor/atualizar_vendedor.php'
    );

    $_SESSION['error_message'] = 'Erro ao atualizar vendedor. Verifique os dados e tente novamente.';
    header("Location: ../../view/vendedor/editar_vendedor.php?id=$id");
    exit;
}
