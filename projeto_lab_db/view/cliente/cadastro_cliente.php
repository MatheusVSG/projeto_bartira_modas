<?php
session_start();
include_once '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Cadastro de Cliente</title>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-2">Cadastro de Cliente</h2>

            <form action="../../controller/cliente/salvar_cliente.php" method="POST">
                <div class="mb-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" required class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" required class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" required class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="estado" class="form-label">Estado (UF)</label>
                    <input type="text" name="estado" id="estado" class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select name="sexo" id="sexo" class="form-control form-control-sm">
                        <option value="">Selecione o sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                <?php
    if ($_SESSION['tipo_usuario'] == 'admin') {
        echo '<a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>';
    } elseif ($_SESSION['tipo_usuario'] == 'vendedor') {
        echo '<a href="../vendedor/home_vendedor.php" class="btn btn-secondary btn-sm">Voltar</a>';
    }
    ?>
                    <button type="submit" name="cadastrar" class="btn btn-success btn-sm">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
