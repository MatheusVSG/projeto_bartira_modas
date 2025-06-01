<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $mensagem_sucesso = 'Administrador cadastrado com sucesso!';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Administrador</title>
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
                'caminho' => 'listar_administrador.php',
                'titulo' => 'Administradores Cadastrados',
                'cor' => 'btn-primary',
            ]
        ];

        include '../../components/barra_navegacao.php';
        ?>

        <h4 class="text-warning">
            Cadastro de Administrador
        </h4>

        <div class="bg-light rounded p-4">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem_sucesso ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <form action="../../controller/administrador/administrador_controller.php" method="POST" class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" name="usuario" id="usuario" required class="form-control" placeholder="Digite o nome de usuário">
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" required class="form-control" placeholder="Digite a senha">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="cadastrar" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>