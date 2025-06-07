<?php
session_start();
include_once '../../connection.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para realizar esta ação.';
    header("Location: ../../login.php");
    exit();
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cadastrar_meta'])) {
    $_SESSION['error_message'] = 'Requisição inválida.';
    header("Location: ../../view/administrador/metas_funcionario.php"); // Corrigido o redirecionamento
    exit();
}

// Valida e sanitiza os dados de entrada
$vendedor_id = filter_input(INPUT_POST, 'vendedor_id', FILTER_VALIDATE_INT);
$meta_valor = filter_input(INPUT_POST, 'meta_valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$data_validade = $_POST['data_validade'] ?? null;

// Validações básicas
$errors = [];

if (!$vendedor_id || $vendedor_id <= 0) {
    $errors[] = 'Selecione um vendedor válido.';
}

if ($meta_valor <= 0) {
    $errors[] = 'O valor da meta deve ser maior que zero.';
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_validade) || strtotime($data_validade) < strtotime('today')) {
    $errors[] = 'Data de validade inválida. Deve ser uma data futura no formato AAAA-MM-DD.';
}

if (count($errors) > 0) {
    $_SESSION['error_message'] = implode('<br>', $errors);
    $_SESSION['old_input'] = $_POST;
    header("Location: ../../view/administrador/metas_funcionario.php");
    exit();
}

try {
    // Inicia transação
    mysqli_begin_transaction($conn);

    // Prepara a query para inserir a meta
    $sql = "INSERT INTO meta_vendas (fk_vendedor_id, valor, data_validade, data_criacao) 
            VALUES (?, ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ids", $vendedor_id, $meta_valor, $data_validade);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Erro ao salvar a meta: ' . mysqli_error($conn));
    }

    // Confirma a transação
    mysqli_commit($conn);

    $_SESSION['success_message'] = 'Meta cadastrada com sucesso!';
} catch (Exception $e) {
    // Rollback em caso de erro
    mysqli_rollback($conn);
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['old_input'] = $_POST;
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}

// Redireciona de volta para o formulário
header("Location: ../../view/administrador/metas_funcionario.php");
exit();
