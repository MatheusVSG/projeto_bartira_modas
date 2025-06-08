<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Administrador não autenticado!';
    header("Location: ../../");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['excluir'])) {
    $_SESSION['error_message'] = 'Acesso inválido. Requisição não permitida!';
    header("Location: ../../view/administrador/listar_administrador.php");
    exit;
}

require_once '../../connection.php';
require_once '../logs/logger.controller.php';



// Operação de Cadastro
if (isset($_POST['cadastrar'])) {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    if (empty(trim($usuario)) || empty(trim($senha))) {
        $_SESSION['error_message'] = 'Usuário e senha são obrigatórios!';
        header("Location: ../../view/administrador/cadastro_administrador.php");
        exit;
    }

    try {
        // Verifica se usuário já existe
        $sql = "SELECT id FROM administrador WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = 'O nome de usuário já está em uso. Por favor, escolha outro.';
            header("Location: ../../view/administrador/cadastro_administrador.php");
            exit;
        }

        // Cadastra novo administrador
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO administrador (usuario, senha) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $senha_hash);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Administrador cadastrado com sucesso!';
            header("Location: ../../view/administrador/cadastro_administrador.php");
            exit;
        } else {
            throw new Exception('Erro ao cadastrar administrador: ' . $stmt->error);
        }
    } catch (Exception $e) {
        registrar_log(
            $conn,
            'Erro ao cadastrar administrador',
            $e->getMessage(),
            $_SERVER['REQUEST_URI'],
            'controller/administrador/administrador_controller.php'
        );

        $_SESSION['error_message'] = 'Erro ao cadastrar administrador. Tente novamente.';
        header("Location: ../../view/administrador/cadastro_administrador.php");
        exit;
    }
}

// Operação de Edição
if (isset($_POST['editar'])) {
    $id = $_POST['id'];

    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    if (empty(trim($usuario)) || empty(trim($senha))) {
        $_SESSION['error_message'] = 'Usuário e senha são obrigatórios!';
        header("Location: ../../view/administrador/cadastro_administrador.php");
        exit;
    }

    if (empty($usuario)) {
        $_SESSION['error_message'] = 'O usuário não pode ficar em branco!';
        header("Location: ../../view/administrador/editar_administrador.php?id=$id");
        exit;
    }

    try {
        // Verifica se o novo usuário já existe (excluindo o próprio registro)
        $sql = "SELECT id FROM administrador WHERE usuario = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = 'O nome de usuário já está em uso. Por favor, escolha outro.';
            header("Location: ../../view/administrador/editar_administrador.php?id=$id");
            exit;
        }

        // Atualiza administrador
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE administrador SET usuario=?, senha=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $usuario, $senha_hash, $id);
        } else {
            $sql = "UPDATE administrador SET usuario=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $usuario, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Administrador atualizado com sucesso!';
            header("Location: ../../view/administrador/listar_administrador.php");
            exit;
        } else {
            throw new Exception('Erro ao atualizar administrador: ' . $stmt->error);
        }
    } catch (Exception $e) {
        registrar_log(
            $conn,
            'Erro ao atualizar administrador',
            $e->getMessage(),
            $_SERVER['REQUEST_URI'],
            'controller/administrador/administrador_controller.php'
        );

        $_SESSION['error_message'] = 'Erro ao atualizar administrador. Tente novamente.';
        header("Location: ../../view/administrador/editar_administrador.php?id=$id");
        exit;
    }
}

// Operação de Exclusão
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    try {
        // Verifica se é o último administrador
        $sql = "SELECT COUNT(*) as total FROM administrador";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        if ($row['total'] <= 1) {
            $_SESSION['error_message'] = 'Não é possível excluir o último administrador!';
            header("Location: ../../view/administrador/cadastro_administrador.php");
            exit;
        }

        // Executa a exclusão
        $sql = "DELETE FROM administrador WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Administrador excluído com sucesso!';
        } else {
            throw new Exception('Erro ao excluir administrador: ' . $stmt->error);
        }
    } catch (Exception $e) {
        registrar_log(
            $conn,
            'Erro ao excluir administrador',
            $e->getMessage(),
            $_SERVER['REQUEST_URI'],
            'controller/administrador/administrador_controller.php'
        );

        $_SESSION['error_message'] = 'Erro ao excluir administrador. Tente novamente.';
    }

    $conn->close();
    header("Location: ../../view/administrador/cadastro_administrador.php");
    exit;
}
