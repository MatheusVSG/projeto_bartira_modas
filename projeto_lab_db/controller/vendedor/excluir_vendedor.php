<?php
session_start();

// Verifica se o método de requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $_SESSION['error_message'] = 'Método de requisição inválido!';
    header("Location: ../../view/vendedor/listar_vendedores.php");
    exit;
}

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Permissão insuficiente!';
    header("Location: ../../login.php");
    exit;
}

include_once '../../connection.php';
include('../logs/logger.controller.php');

// Validação segura do ID
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'ID do vendedor inválido!';
    header("Location: ../../view/vendedor/listar_vendedores.php");
    exit;
}

$id = intval($_GET['id']);

try {
    // Verifica se o vendedor existe antes de tentar excluir
    $check_sql = "SELECT id FROM vendedores WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        $_SESSION['warning_message'] = 'Vendedor não encontrado!';
        header("Location: ../../view/vendedor/listar_vendedores.php");
        exit;
    }

    // Verifica se há metas de vendas associadas
    $check_metas_sql = "SELECT COUNT(*) as total FROM meta_vendas WHERE fk_vendedor_id = ?";
    $check_metas_stmt = $conn->prepare($check_metas_sql);
    $check_metas_stmt->bind_param("i", $id);
    $check_metas_stmt->execute();
    $check_metas_stmt->bind_result($total_metas);
    $check_metas_stmt->fetch();
    $check_metas_stmt->close();

    if ($total_metas > 0) {
        // Primeiro exclui as metas de vendas associadas
        $delete_metas_sql = "DELETE FROM meta_vendas WHERE fk_vendedor_id = ?";
        $delete_metas_stmt = $conn->prepare($delete_metas_sql);

        if (!$delete_metas_stmt) {
            throw new Exception('Erro ao preparar a exclusão de metas: ' . $conn->error);
        }

        $delete_metas_stmt->bind_param("i", $id);

        if (!$delete_metas_stmt->execute()) {
            throw new Exception('Erro ao excluir metas do vendedor: ' . $delete_metas_stmt->error);
        }

        $delete_metas_stmt->close();
    }

    // Agora exclui o vendedor
    $delete_sql = "DELETE FROM vendedores WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);

    if (!$delete_stmt) {
        throw new Exception('Erro ao preparar a consulta de exclusão: ' . $conn->error);
    }

    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            $_SESSION['success_message'] = 'Vendedor' . ($total_metas > 0 ? ' e suas metas associadas' : '') . ' excluído(s) com sucesso!';

            // Registra o log de sucesso
            registrar_log(
                $conn,
                'Exclusão de vendedor',
                'Vendedor ID ' . $id . ' e ' . $total_metas . ' meta(s) excluída(s) por usuário ID ' . $_SESSION['usuario_id'],
                $_SERVER['REQUEST_URI'],
                'controller/vendedor/excluir_vendedor.php'
            );
        } else {
            $_SESSION['warning_message'] = 'Nenhum vendedor foi excluído.';
        }
    } else {
        throw new Exception('Erro ao executar a exclusão: ' . $delete_stmt->error);
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao excluir vendedor',
        $e->getMessage() . ' - ID: ' . $id,
        $_SERVER['REQUEST_URI'],
        'controller/vendedor/excluir_vendedor.php'
    );

    $_SESSION['error_message'] = 'Erro ao excluir vendedor: ' . $e->getMessage();
}

// Redireciona de volta para a lista de vendedores
header("Location: ../../view/vendedor/listar_vendedores.php");
exit;
