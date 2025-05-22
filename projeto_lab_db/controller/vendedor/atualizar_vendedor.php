<?php
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
$sexo = $_POST['sexo'];
$modificado_por = 1;
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE vendedores SET nome=?, cpf=?, email=?, telefone=?, logradouro=?, numero=?, bairro=?, cidade=?, estado=?, sexo=?, modificado_por=?, senha=? WHERE id=?");


$stmt->bind_param("ssssssssssisi", $nome, $cpf, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $sexo, $modificado_por, $senha, $id);

if ($stmt->execute()) {
    header("Location: ../../view/vendedor/cadastro_vendedor.php");
    exit;
} else {
    registrar_log(
        $conn,
        'Erro ao atualizar vendedor',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/vendedor/atualizar_vendedor.php'
    );
    echo "Erro ao atualizar: " . $stmt->error;
}
?>

