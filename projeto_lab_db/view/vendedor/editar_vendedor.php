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
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <div class="d-flex justify-content-end mb-2 gap-2">
            <a href="listar_vendedores.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar</a>
        </div>
        <h1 class="text-center text-warning mb-4">Editar Vendedor</h1>

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
            <div class="d-flex justify-content-start gap-2 mt-3">
                <button type="submit" name="editar" class="btn btn-success">Atualizar</button>
            </div>
        </form>
    </div>
</body>

</html>