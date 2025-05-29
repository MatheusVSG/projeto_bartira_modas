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
    <title>Bartira Modas | Cadastro de Administrador</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <a href="home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
        <div class="col-12 col-md-6 bg-light p-5 rounded shadow position-relative">
            <div class="d-flex justify-content-end mb-2 gap-2">
                <!-- Botão de voltar já está fixo acima -->
            </div>
            <h2 class="text-center text-dark mb-4">Cadastro de Administrador</h2>

            <form action="../../controller/administrador/administrador_controller.php" method="POST">
                <div class="mb-4">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" name="usuario" id="usuario" required class="form-control">
                </div>

                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" required class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="home_adm.php" class="btn btn-secondary">Voltar</a>
                    <button type="submit" name="cadastrar" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>