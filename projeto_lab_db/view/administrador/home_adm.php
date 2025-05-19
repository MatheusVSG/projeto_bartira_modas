<?php
session_start();
include '../../connection.php';
include '../../head.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = "Você precisa estar logado como administrador para acessar essa página.";
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Home Administrador</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container-fluid bg-dark text-light p-4">
        <h1 class="mb-4 text-warning text-center">Painel do Administrador</h1>

        <div class="row justify-content-center gap-4">


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Cadastro de Administrador</h4>
                        <p class="card-text">Gerenciar contas de administradores do sistema.</p>
                        <a href="../administrador/listar_administrador.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Cadastro de Vendedor</h4>
                        <p class="card-text">Adicionar ou editar vendedores.</p>
                        <a href="../vendedor/cadastro_vendedor.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Cadastro de Cliente</h4>
                        <p class="card-text">Gerenciar os dados dos clientes.</p>
                        <a href="../cliente/cadastro_cliente.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Estoque</h4>
                        <p class="card-text">Visualizar e atualizar o estoque.</p>
                        <a href="../estoque/listar_estoque.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Cadastro de Produto</h4>
                        <p class="card-text">Adicionar e editar produtos.</p>
                        <a href="../produto/cadastro-produto.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Forma de Pagamento</h4>
                        <p class="card-text">Gerenciar métodos de pagamento.</p>
                        <a href="../forma_pagto/listar_forma_pagto.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Metas de Venda</h4>
                        <p class="card-text">Definir metas de vendas por funcionário.</p>
                        <a href="../administrador/metas_funcionario.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Vendas</h4>
                        <p class="card-text">Visualizar as vendas feitas pelos vendedores.</p>
                        <a href="../venda/listar_vendas.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>


            <div class="col-10 col-md-3">
                <div class="card bg-light text-center shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h4 class="card-title text-dark">Logs do Sistema</h4>
                        <p class="card-text">Consultar ações realizadas no sistema.</p>
                        <a href="../logs/listar_logs.php" class="btn btn-primary mt-3">Acessar</a>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <a href="../logout.php" class="btn btn-danger position-absolute" style="top: 20px; right: 20px;">Sair</a>
            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>