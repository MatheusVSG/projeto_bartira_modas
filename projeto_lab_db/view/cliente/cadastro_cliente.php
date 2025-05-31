<?php
session_start();
include_once '../../connection.php';

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
    <?php include '../../head.php'; ?>

    <title>Bartira Modas | Cadastro de Cliente</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../vendedor/home_vendedor.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],
            
            [
                'caminho' => 'listar_clientes.php',
                'titulo' => 'Clientes Cadastrados',
                'cor' => 'btn-primary',
            ]
        ];

        include '../../../components/barra_navegacao.php';
        ?>

        <h4 class="text-warning mb-0">
            Cadastro de Cliente
        </h4>

        <div class="bg-light rounded p-4">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem_sucesso ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <form action=" ../../controller/cliente/salvar_cliente.php" method="POST" class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" required class="form-control" placeholder="Digite o nome">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" required class="form-control" pattern="\d{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')" title="Digite exatamente 11 números, somente dígitos." placeholder="000.000.000-00">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" required placeholder="Digite o e-mail" class="form-control">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control" placeholder="Digite o telefone">
                </div>

                <div class="col-11 col-lg-4 mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control" placeholder="Digite o logradouro">
                </div>

                <div class="col-2 col-lg-1 mb-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" class="form-control" placeholder="Ex.: 10">
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Digite o bairro">
                </div>

                <div class="col-12 col-lg 3 mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Digite a cidade">
                </div>

                <div class="col-2 col-lg-1 mb-2">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" id="estado" class="form-control" placeholder="Digite o estado (UF)">
                </div>

                <div class="mb-3">
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

    </div>
</body>

</html>