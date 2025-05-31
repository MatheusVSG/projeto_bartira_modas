<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['excluido']) && $_GET['excluido'] == 1) {
    $mensagem_sucesso = 'Vendedor excluído com sucesso!';
} elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1) {
    $mensagem_sucesso = 'Vendedor atualizado com sucesso!';
}

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_vendedor.php',
        'titulo' => 'Novo Vendedor',
        'cor' => 'btn-primary'
    ]
];

$sql = "SELECT * FROM vendedores ORDER BY nome";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Lista de Vendedores</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">
                Lista de Vendedores
            </h4>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive mt-3">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendedor = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $vendedor['id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($vendedor['nome']) ?></td>
                                    <td data-label="CPF"><?= htmlspecialchars($vendedor['cpf']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($vendedor['email']) ?></td>
                                    <td data-label="Tipo">
                                        <span class="status-badge <?= $vendedor['tipo'] === 'admin' ? 'badge-admin' : 'badge-vendedor' ?>">
                                            <?= $vendedor['tipo'] === 'admin' ? 'Administrador' : 'Vendedor' ?>
                                        </span>
                                    </td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_vendedor.php?id=<?= $vendedor['id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/vendedor/excluir_vendedor.php?id=<?= $vendedor['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhum vendedor cadastrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>