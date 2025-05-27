<?php
session_start();
include '../../connection.php';
include '../../head.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'vendedor') {
    $_SESSION['error_message'] = "Você precisa estar logado como Vendedor para acessar essa página.";
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Home Vendedor</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-dark text-light min-vh-100">
    <div class="container py-5">

        <a href="../logout.php" class="btn btn-danger position-absolute" style="top: 20px; right: 20px;">Sair</a>

        <h1 class="mb-5 text-warning text-center">Painel do Vendedor</h1>

        <div class="row justify-content-center">

            <div class="col-10 col-md-3 mb-4">
                <div class="card bg-light text-center shadow-lg rounded-3 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Cadastro de Cliente</h4>
                        <p class="card-text">Gerenciar os dados dos clientes.</p>
                        <a href="../cliente/cadastro_cliente.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-3 mb-4">
                <div class="card bg-light text-center shadow-lg rounded-3 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Estoque</h4>
                        <p class="card-text">Visualizar o estoque.</p>
                        <a href="../estoque/listar_estoque.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-3 mb-4">
                <div class="card bg-light text-center shadow-lg rounded-3 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Metas de Venda</h4>
                        <p class="card-text">Visualizar minhas metas de vendas.</p>
                        <a href="../vendedor/meta_vendedor.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-3 mb-4">
                <div class="card bg-light text-center shadow-lg rounded-3 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Vendas</h4>
                        <p class="card-text">Fazer vendas.</p>
                        <a href="../venda/cadastrar_venda.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</body>

</html>