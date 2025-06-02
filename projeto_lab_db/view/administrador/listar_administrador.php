<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['excluido']) && $_GET['excluido'] == 1) {
    $mensagem_sucesso = 'Administrador excluído com sucesso!';
} elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1) {
    $mensagem_sucesso = 'Administrador atualizado com sucesso!';
}

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_administrador.php',
        'titulo' => 'Novo Administrador',
        'cor' => 'btn-primary'
    ]
];

$sql = "SELECT * FROM administrador ORDER BY usuario";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Lista de Administradores</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">
                Lista de Administradores
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
                                <th>Usuário</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($admin = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $admin['id'] ?></td>
                                    <td data-label="Usuário"><?= htmlspecialchars($admin['usuario']) ?></td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_administrador.php?id=<?= $admin['id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/administrador/administrador_controller.php?excluir=<?= $admin['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este administrador?')">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhum administrador cadastrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>