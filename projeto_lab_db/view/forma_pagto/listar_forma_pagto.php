<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM forma_pagto");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Formas de Pagamento</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 bg-light p-4 rounded shadow">
            <h2 class="text-center text-dark mb-4">Formas de Pagamento</h2>

            <a href="cadastrar_forma_pagto.php" class="btn btn-primary mb-3">Nova Forma de Pagamento</a>

            <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['descricao'] ?></td>
                            <td>
                                <form method="POST" action="../../controller/forma_pagto_controller.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="excluir_pagto" class="btn btn-danger btn-sm" onclick="return confirm('Excluir?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>