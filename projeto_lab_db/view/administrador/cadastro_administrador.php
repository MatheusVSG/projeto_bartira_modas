<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
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

        <!-- Mensagens Sucesso/Erro -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }
            ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <h4 class="text-warning mb-4">
            Cadastro de Administrador
        </h4>

        <div class="bg-light rounded p-4">
            <form action="../../controller/administrador/administrador_controller.php" method="POST" class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="usuario" class="form-label">Usuário <span class="text-danger">*</span></label>
                    <input type="text" name="usuario" id="usuario" required placeholder="Digite o nome de usuário" class="form-control">
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                    <input type="password" name="senha" id="senha" required placeholder="Digite a senha" class="form-control">
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
                    <button type="reset" class="btn btn-warning">
                        Limpar
                    </button>

                    <button type="submit" name="cadastrar" class="btn btn-success">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>