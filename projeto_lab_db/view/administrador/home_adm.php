<?php
session_start();
include '../../connection.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = "Você precisa estar logado como administrador para acessar essa página.";
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../../head.php'; ?>
    <title>Home Administrador</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-warning text-center w-100">Painel do Administrador</h1>
            <a href="../logout.php" class="btn btn-danger position-absolute" style="top: 20px; right: 20px;">Sair</a>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Cada card -->
            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Cadastro de Administrador</h4>
                        <p class="card-text">Gerenciar contas de administradores do sistema.</p>
                        <a href="../administrador/listar_administrador.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Cadastro de Vendedor</h4>
                        <p class="card-text">Adicionar ou editar vendedores.</p>
                        <a href="../vendedor/cadastro_vendedor.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Cadastro de Cliente</h4>
                        <p class="card-text">Gerenciar os dados dos clientes.</p>
                        <a href="../cliente/cadastro_cliente.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Estoque</h4>
                        <p class="card-text">Visualizar e atualizar o estoque.</p>
                        <a href="../estoque/listar_estoque.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Cadastro de Produto</h4>
                        <p class="card-text">Adicionar e editar produtos.</p>
                        <a href="../produto/cadastro-produto.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Forma de Pagamento</h4>
                        <p class="card-text">Gerenciar métodos de pagamento.</p>
                        <a href="../forma_pagto/listar_forma_pagto.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Metas de Venda</h4>
                        <p class="card-text">Definir metas de vendas por funcionário.</p>
                        <a href="../administrador/metas_funcionario.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Vendas</h4>
                        <p class="card-text">Visualizar as vendas feitas pelos vendedores.</p>
                        <a href="../venda/listar_vendas.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-4 col-lg-3">
                <div class="card bg-light text-dark shadow h-100 text-center">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title">Logs do Sistema</h4>
                        <p class="card-text">Consultar ações realizadas no sistema.</p>
                        <a href="../logs/listar_logs.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>