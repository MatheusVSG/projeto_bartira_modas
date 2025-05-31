<?php
session_start();
include '../../connection.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
$result = $conn->query("SELECT * FROM administrador");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Administradores Cadastrados</title>
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <a href="home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>

        <h2 class="text-center text-warning mb-4">Administradores Cadastrados</h2>

        <div class="text-dark p-4 rounded shadow">
            <div class="text-end mb-3">
                <a href="cadastro_administrador.php" class="btn btn-primary btn-sm">Novo Administrador</a>
            </div>

            <div class="table-responsive">
                <table class="custom-table ">
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

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
</body>

</html>