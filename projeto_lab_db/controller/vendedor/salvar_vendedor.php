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

// Armazena TODOS os dados do POST na sessão
$_SESSION['form_data'] = $_POST;

try {
    // Sanitização dos dados
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
    $sexo = $_POST['sexo'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = $_POST['estado'] ?? '';
    $modificado_por = $_SESSION['usuario_id'];

    // Validações
    $erros = [];
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if (strlen($cpf) !== 11) $erros[] = 'CPF deve conter 11 dígitos';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido';
    if (strlen($senha) < 6) $erros[] = 'Senha deve ter no mínimo 6 caracteres';

    if (!empty($erros)) {
        throw new Exception(implode('<br>', $erros));
    }

    // Inicia transação
    $conn->begin_transaction();

    // Verifica se CPF já existe
    $verifica_cpf = $conn->prepare("SELECT id FROM vendedores WHERE cpf = ?");
    $verifica_cpf->bind_param("s", $cpf);
    $verifica_cpf->execute();
    
    if ($verifica_cpf->get_result()->num_rows > 0) {
        throw new Exception('CPF já cadastrado para outro vendedor');
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Prepara e executa a inserção
    $stmt = $conn->prepare("INSERT INTO vendedores 
        (nome, cpf, email, telefone, sexo, senha, logradouro, numero, bairro, cidade, estado, modificado_por) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssssssssi", 
        $nome, $cpf, $email, $telefone, $sexo, $senha_hash,
        $logradouro, $numero, $bairro, $cidade, $estado, $modificado_por);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao cadastrar vendedor: ' . $stmt->error);
    }

    // Commit da transação
    $conn->commit();

    // Limpa os dados do formulário após sucesso
    unset($_SESSION['form_data']);
    $_SESSION['success_message'] = 'Vendedor cadastrado com sucesso!';
    header("Location: ../../view/vendedor/listar_vendedores.php");
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
?>