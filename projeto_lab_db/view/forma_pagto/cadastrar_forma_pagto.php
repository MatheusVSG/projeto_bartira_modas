<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Cadastro de Forma de Pagamento</title>
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
            <a href="listar_forma_pagto.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar</a>
        </div>
        <h1 class="text-center text-warning mb-4">Cadastrar Forma de Pagamento</h1>

        <form action="../../controller/forma_pagto/forma_pagto_controller.php" method="POST" class="bg-light text-dark p-4 rounded shadow">
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" name="descricao" id="descricao" required class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="cadastrar_pagto" class="btn btn-success btn-sm">Cadastrar</button>
            </div>
        </form>
    </div>
</body>

</html>