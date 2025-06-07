<?php
session_start(); // Garante que a sessão está iniciada para usar $_SESSION
include_once '../../connection.php';
include('../logs/logger.controller.php');

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'ID do cliente não fornecido para exclusão.';
    header("Location: ../../view/cliente/listar_clientes.php");
    exit();
}

$id = $_GET['id'];

try {
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Erro ao preparar consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = 'Cliente excluído com sucesso!';
    } else {
        $_SESSION['error_message'] = 'Cliente não encontrado ou já excluído.';
    }
    $stmt->close();

} catch (Exception $e) {
    $error_message = $e->getMessage();
    // Verifica se é um erro de chave estrangeira (cliente com vendas associadas)
    if (strpos($error_message, 'Cannot delete or update a parent row: a foreign key constraint fails') !== false || $conn->errno == 1451) {
        $_SESSION['error_message'] = 'Não foi possível excluir o cliente. Existem vendas associadas a ele.';
    } else {
        // Erro genérico
        $_SESSION['error_message'] = 'Erro ao excluir cliente: ' . $error_message;
        // Registrar o erro para depuração
        registrar_log(
            $conn,
            'Erro inesperado ao excluir cliente',
            $error_message,
            $_SERVER['REQUEST_URI'],
            'controller/cliente/excluir_cliente.php'
        );
    }
}

$conn->close();

header("Location: ../../view/cliente/listar_clientes.php");
exit();
