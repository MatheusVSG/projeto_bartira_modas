<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $mensagem_sucesso = 'Forma de pagamento cadastrada com sucesso!';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Forma de Pagamento</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../administrador/home_adm.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],
            [
                'caminho' => 'listar_forma_pagto.php',
                'titulo' => 'Formas de Pagamento Cadastradas',
                'cor' => 'btn-primary',
            ]
        ];
        include '../../../components/barra_navegacao.php';
        ?>

        <h4 class="text-warning mb-0">Cadastro de Forma de Pagamento</h4>

        <div class="bg-light rounded p-4 mt-3">
            <?php if (!empty($mensagem_sucesso)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem_sucesso ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <form action="../../controller/forma_pagto_controller.php" method="POST" class="row">
                <div class="col-12 mb-3">
                    <label for="descricao" class="form-label">Descrição da Forma de Pagamento:</label>
                    <input type="text" name="descricao" id="descricao" class="form-control" required>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="cadastrar_pagto" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>