<?php
session_start();
include_once '../../connection.php';
include '../../components/estados.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado.';
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'Vendedor não identificado.';
    header("Location: editar_vendedor.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM vendedores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo "Vendedor não encontrado.";
    exit;
}

$vendedor = $res->fetch_assoc();

$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../../' : '../vendedor/home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'listar_vendedores.php',
        'titulo' => 'Vendedores Cadastrados',
        'cor' => 'btn-primary'
    ]
];

include '../../components/estados.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Editar Vendedor</title>
</head>

<body>
    <div class="w-100 min-vh-100 justify-content-center align-items-center bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php } ?>
        </div>

        <h4 class="text-warning">Editar Vendedor</h4>

        <div class="bg-light p-4 rounded">
            <form action="../../controller/vendedor/atualizar_vendedor.php" method="POST" class="row">
                <input type="hidden" name="id" value="<?= $vendedor['id'] ?>">

                <div class="col-12 col-lg-4 mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control form-control-sm" required value="<?= htmlspecialchars($vendedor['nome']) ?>">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control form-control-sm" required maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')" value="<?= htmlspecialchars($vendedor['cpf']) ?>">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" required value="<?= htmlspecialchars($vendedor['email']) ?>">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control" maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')" value="<?= htmlspecialchars($vendedor['telefone']) ?>">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control" value="<?= htmlspecialchars($vendedor['logradouro']) ?>">
                </div>

                <div class="col-12 col-lg-1 mb-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" class="form-control" value="<?= htmlspecialchars($vendedor['numero']) ?>">
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" value="<?= htmlspecialchars($vendedor['bairro']) ?>">
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" value="<?= htmlspecialchars($vendedor['cidade']) ?>">
                </div>

                <div class="col-12 col-lg-1 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($estados as $estado) { ?>
                            <option value="<?= $estado ?>" <?= $estado === $vendedor['estado'] ? 'selected' : '' ?>><?= $estado ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select name="sexo" id="sexo" class="form-select">
                        <option value="M" <?= $vendedor['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                        <option value="F" <?= $vendedor['sexo'] == 'F' ? 'selected' : '' ?>>Feminino</option>
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" required value="<?= htmlspecialchars($vendedor['senha']) ?>">
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">
                    <a href="listar_vendedores.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>