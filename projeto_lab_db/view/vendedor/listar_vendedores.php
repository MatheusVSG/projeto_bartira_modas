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
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <h4 class="text-warning mb-0">
            Lista de Vendedores
        </h4>

        <div class="bg-light rounded p-4 mt-3">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Nome</th>
                                <th class="text-nowrap">CPF</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Tipo</th>
                                <th class="text-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendedor = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $vendedor['id'] ?></td>
                                    <td><?= htmlspecialchars($vendedor['nome']) ?></td>
                                    <td><?= htmlspecialchars($vendedor['cpf']) ?></td>
                                    <td><?= htmlspecialchars($vendedor['email']) ?></td>
                                    <td><?= $vendedor['tipo_usuario'] === 'admin' ? 'Administrador' : 'Vendedor' ?></td>
                                    <td class="text-nowrap">
                                        <a href="editar_vendedor.php?id=<?= $vendedor['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="../../controller/vendedor/excluir_vendedor.php?id=<?= $vendedor['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Nenhum vendedor cadastrado.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>