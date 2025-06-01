<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado.';
    header("Location: ../../");
    exit();
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

$result = mysqli_query($conn, "SELECT * FROM administrador ORDER BY usuario");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Administradores</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <!-- Mensagens Sucesso/Erro -->
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

        <h4 class="text-warning">Administradores Cadastrados</h4>

        <div class="mb-3 d-flex flex-wrap gap-2">
            <?php foreach ($linksAdicionais as $link): ?>
                <a href="<?= $link['caminho'] ?>" class="btn <?= $link['cor'] ?>"><?= $link['titulo'] ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td data-label="ID"><?= $row['id'] ?></td>
                                <td data-label="Usuário"><?= htmlspecialchars($row['usuario']) ?></td>
                                <td data-label="Ações">
                                    <div class="action-buttons">
                                        <a href="editar_administrador.php?id=<?= $row['id'] ?>" class="action-btn btn-edit">Editar</a>
                                        <a href="../../controller/administrador/administrador_controller.php?excluir=<?= $row['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este administrador?')">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-results">Nenhum administrador encontrado.</div>
        <?php endif; ?>
    </div>
</body>

</html>