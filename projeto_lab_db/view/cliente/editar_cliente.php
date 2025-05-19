<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {

    header("Location: ../../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM clientes WHERE id = $id";
$result = $conn->query($sql);
$cliente = $result->fetch_assoc();
?>

<form action="../../controller/atualizar_cliente.php" method="post">
    <input type="hidden" name="id" value="<?= $cliente['id'] ?>">
    <input type="text" name="nome" value="<?= $cliente['nome'] ?>" required><br>
    <input type="text" name="cpf" value="<?= $cliente['cpf'] ?>" required><br>
    <input type="email" name="email" value="<?= $cliente['email'] ?>" required><br>

    <input type="submit" value="Atualizar">
</form>