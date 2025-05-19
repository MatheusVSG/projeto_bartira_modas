<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include './head.php'; ?>
    <title>Bartira Modas | Página Inicial</title>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="d-flex flex-column justify-content-center align-items-center ">
            <img src="./assets/img/index/img_index.png" alt="" class="logo">
            <h1 class="text-warning text-center">Bartira Modas</h1>
            <p class="text-light text-center fs-4">Vendas online</p>
        </div>

        <div class="w-100 row mx-auto align-items-center">

            <div class="col-12 col-md-6 d-flex flex-column align-items-center">
                <h3 class="text-light">Vendedor</h3>
                <form class="col-12 col-md-6 bg-light px-3 py-5 rounded" method="POST" action="login.php">
                    <div class="mb-5">
                        <label for="cpf">CPF</label>
                        <input type="text" name="cpf" id="cpf" maxlength="11" required class="form-control">
                    </div>

                    <div class="mb-5">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" maxlength="255" required class="form-control">
                    </div>

                    <button type="submit" name="login_vendedor" class="btn btn-primary">Acessar</button>
                </form>
            </div>


            <div class="col-12 col-md-6 d-flex flex-column align-items-center">
                <h3 class="text-light">Administrador</h3>
                <form class="col-12 col-md-6 bg-light px-3 py-5 rounded" method="POST" action="login.php">
                    <div class="mb-5">
                        <label for="usuario">Usuário</label>
                        <input type="text" name="usuario" id="usuario" maxlength="255" required class="form-control">
                    </div>

                    <div class="mb-5">
                        <label for="senha_admin">Senha</label>
                        <input type="password" name="senha" id="senha_admin" maxlength="255" required class="form-control">
                    </div>

                    <button type="submit" name="login_admin" class="btn btn-primary">Acessar</button>
                </form>

            </div>
        </div>
    </div>
</body>

</html>