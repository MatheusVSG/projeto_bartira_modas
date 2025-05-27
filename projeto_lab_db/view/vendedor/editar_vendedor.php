<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}


if (!isset($_GET['id'])) {
    echo "ID do vendedor não especificado.";
    exit();
}

$id = intval($_GET['id']);

$res = $conn->query("SELECT * FROM vendedores WHERE id = $id");

if (!$res || $res->num_rows == 0) {
    echo "Vendedor não encontrado.";
    exit();
}

$row = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Editar Vendedor</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <h1 class="text-center text-warning">Editar Vendedor</h1>
        <form action="../../controller/vendedor/atualizar_vendedor.php" method="post" class="bg-light text-dark p-4 rounded shadow">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="nome" class="form-control mb-2" value="<?= $row['nome'] ?>" required>
            <input type="text" name="cpf" class="form-control mb-2" value="<?= $row['cpf'] ?>" required>
            <input type="email" name="email" class="form-control mb-2" value="<?= $row['email'] ?>" required>
            <input type="text" name="telefone" class="form-control mb-2" value="<?= $row['telefone'] ?>">
            <input type="text" name="logradouro" class="form-control mb-2" value="<?= $row['logradouro'] ?>">
            <input type="text" name="numero" class="form-control mb-2" value="<?= $row['numero'] ?>">
            <input type="text" name="bairro" class="form-control mb-2" value="<?= $row['bairro'] ?>">
            <input type="text" name="cidade" class="form-control mb-2" value="<?= $row['cidade'] ?>">
            <input type="text" name="estado" class="form-control mb-2" value="<?= $row['estado'] ?>">
            <select name="sexo" class="form-control mb-2">
                <option value="M" <?= $row['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= $row['sexo'] == 'F' ? 'selected' : '' ?>>Feminino</option>
            </select>
            <input type="password" name="senha" class="form-control mb-2" value="<?= $row['senha'] ?>" required>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Voltar</button>
        </form>
    </div>
</body>

</html>