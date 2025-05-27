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

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 bg-light p-4 rounded shadow">
            <h2 class="text-center text-dark mb-4">Cadastrar Forma de Pagamento</h2>

            <form action="../../controller/forma_pagto_controller.php" method="POST">
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" name="descricao" id="descricao" required class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>
                    <button type="submit" name="cadastrar_pagto" class="btn btn-success btn-sm">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>