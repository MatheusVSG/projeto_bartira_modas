<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
$result = $conn->query("SELECT * FROM administrador");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Administradores Cadastrados</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container py-5">
        <a href="home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
        <div class="d-flex justify-content-end mb-2 gap-2">
            <!-- Botão de voltar já está fixo acima -->
        </div>
        <h2 class="text-center text-warning mb-4">Administradores Cadastrados</h2>

        <div class="text-end mb-3">
            <a href="cadastro_administrador.php" class="btn btn-primary">Novo Administrador</a>
        </div>

        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['usuario'] ?></td>
                        <td>
                            <a href="editar_administrador.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="../../controller/administrador/administrador_controller.php?excluir=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>