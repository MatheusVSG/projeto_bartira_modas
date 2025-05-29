<?php
session_start();
include_once '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $mensagem_sucesso = 'Cliente cadastrado com sucesso!';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Cadastro de Cliente</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="w-100 min-vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 bg-light p-4 rounded shadow mb-4">
            <h2 class="text-center text-dark mb-3">Cadastro de Cliente</h2>
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem_sucesso ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>
            <form action="../../controller/cliente/salvar_cliente.php" method="POST">
                <div class="mb-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" required class="form-control form-control-sm" placeholder="Digite o nome">
                </div>
                <div class="mb-2">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" required class="form-control form-control-sm" pattern="\d{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')" title="Digite exatamente 11 números, somente dígitos." placeholder="Digite o CPF (somente números)">
                </div>
                <div class="mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" required class="form-control form-control-sm" placeholder="Digite o e-mail">
                </div>
                <div class="mb-2">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control form-control-sm" placeholder="Digite o telefone">
                </div>
                <div class="mb-2">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control form-control-sm" placeholder="Digite o logradouro">
                </div>
                <div class="mb-2">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control form-control-sm" placeholder="Digite o número">
                </div>
                <div class="mb-2">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control form-control-sm" placeholder="Digite o bairro">
                </div>
                <div class="mb-2">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control form-control-sm" placeholder="Digite a cidade">
                </div>
                <div class="mb-2">
                    <label for="estado" class="form-label">Estado (UF)</label>
                    <input type="text" name="estado" id="estado" class="form-control form-control-sm" placeholder="Digite o estado (UF)">
                </div>
                <div class="mb-2">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select name="sexo" id="sexo" class="form-control form-control-sm">
                        <option value="">Selecione o sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" name="cadastrar" class="btn btn-success btn-sm">Cadastrar</button>
                </div>
            </form>
        </div>
        <div class="col-12 col-sm-10 col-md-8 col-lg-7 bg-light p-4 rounded shadow">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-dark mb-0">Lista de Clientes</h2>
                <a href="listar_clientes.php" class="btn btn-primary btn-sm">Ver Todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui você pode inserir os dados dos clientes -->
                    </tbody>
                </table>
            </div>
        </div>
        <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
    </div>
</body>

</html>