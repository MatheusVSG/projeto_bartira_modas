<?php
session_start();
include_once '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Cadastro de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <h1 class="text-center text-warning">Cadastro de Administrador</h1>

        <form action="../../controller/administrador/administrador_controller.php" method="POST" class="bg-light text-dark p-4 rounded shadow">
            <input type="text" name="usuario" class="form-control mb-2" placeholder="Usuário" required>
            <input type="password" name="senha" class="form-control mb-2" placeholder="Senha" required>
            <button type="submit" name="cadastrar" class="btn btn-success">Salvar</button>
        </form>

        <a href="home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous" defer></script>
</body>

</html>
