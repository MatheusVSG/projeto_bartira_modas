<?php
session_start();
if (isset($_SESSION['tipo_usuario']) && isset($_SESSION['usuario_id'])) {
    if ($_SESSION['tipo_usuario'] === 'vendedor') {
        header('Location: view/vendedor/home_vendedor.php');
        exit;
    } else {
        header('Location: view/administrador/home_adm.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include './head.php'; ?>
    <title>Bartira Modas | Página Inicial</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="w-100 min-vh-100 d-flex flex-column justify-content-center align-items-center position-relative">
        <!-- Alertas -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['warning_message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['warning_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['warning_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php } ?>
        </div>

        <div class="text-center mb-4">
            <img src="./assets/img/index/img_index.png" alt="Logo Bartira Modas" class="logo">
            <h1 class="text-warning">Bartira Modas</h1>
            <p class="text-light fs-4">Vendas online</p>
        </div>


        <div class="row w-100 justify-content-center">

            <div class="col-12 col-md-5 m-2">
                <div class="bg-light text-dark p-4 rounded shadow">
                    <h3 class="text-center mb-4">Acesso do Vendedor</h3>
                    <form method="POST" action="login.php">
                        <input type="hidden" name="login_vendedor">
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" name="cpf" id="cpf" maxlength="11" required class="form-control">
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha" maxlength="255" required class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Acessar</button>
                        </div>
                    </form>
                </div>
            </div>


            <div class="col-12 col-md-5 m-2">
                <div class="bg-light text-dark p-4 rounded shadow">
                    <h3 class="text-center mb-4">Acesso do Administrador</h3>
                    <form method="POST" action="login.php">
                        <input type="hidden" name="login_admin">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuário</label>
                            <input type="text" name="usuario" id="usuario" maxlength="255" required class="form-control">
                        </div>

                        <div class="mb-4">
                            <label for="senha_admin" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha_admin" maxlength="255" required class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Acessar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>