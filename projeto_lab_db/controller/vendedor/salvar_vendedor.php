<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Método de requisição inválido!';
    header("Location: ../../view/vendedor/cadastro_vendedor.php");
    exit();
}

include_once '../../connection.php';
include('../logs/logger.controller.php');

// Armazena os dados do formulário em caso de erro
$_SESSION['form_data'] = $_POST;

try {
    // Validações
    $requiredFields = ['nome', 'cpf', 'email', 'telefone', 'sexo', 'senha'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("O campo " . ucfirst($field) . " é obrigatório!");
        }
    }

    // Verifica se o CPF já está cadastrado
    $sqlCheckCpf = "SELECT id FROM vendedores WHERE cpf = ?";
    $stmtCheck = $conn->prepare($sqlCheckCpf);
    $stmtCheck->bind_param("s", $_POST['cpf']);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        throw new Exception("CPF já cadastrado no sistema!");
    }

    // Inicia transação
    $conn->begin_transaction();

    // Prepara a query de inserção
    $sql = "INSERT INTO vendedores (
        nome, 
        cpf, 
        email, 
        telefone, 
        logradouro, 
        numero, 
        bairro, 
        cidade, 
        estado, 
        sexo, 
        senha
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar a query: " . $conn->error);
    }

    // Hash da senha
    $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Bind dos parâmetros
    $stmt->bind_param(
        "sssssssssss",
        $_POST['nome'],
        $_POST['cpf'],
        $_POST['email'],
        $_POST['telefone'],
        $_POST['logradouro'],
        $_POST['numero'],
        $_POST['bairro'],
        $_POST['cidade'],
        $_POST['estado'],
        $_POST['sexo'],
        $senhaHash
    );

    if (!$stmt->execute()) {
        throw new Exception('Erro ao cadastrar vendedor: ' . $stmt->error);
    }

    // Commit da transação
    $conn->commit();

    // Limpa os dados do formulário após sucesso
    unset($_SESSION['form_data']);
    $_SESSION['success_message'] = 'Vendedor cadastrado com sucesso!';
    header("Location: ../../view/vendedor/cadastro_vendedor.php");
    exit();
} catch (Exception $e) {
    // Rollback em caso de erro
    if (isset($conn) && method_exists($conn, 'rollback')) {
        $conn->rollback();
    }

    // Registra o erro
    registrar_log(
        $conn,
        'Erro ao cadastrar vendedor',
        $e->getMessage(),
        $_SERVER['REQUEST_URI'],
        'controller/vendedor/salvar_vendedor.php'
    );

    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../../view/vendedor/cadastro_vendedor.php");
    exit();
}
