<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para realizar esta ação.';
    header("Location: ../../login.php");
    exit();
}

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
    <div class="w-100 vh-100 d-flex flex-column bg-dark px-3 pb-3">
        <?php
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

        include '../../components/barra_navegacao.php';
        ?>

        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }
            ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <h4 class="text-warning">
            Lista de Administradores
        </h4>

        <div class="flex-grow-1 overflow-y-hidden">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="h-100 overflow-y-auto table-responsive">
                    <table class="custom-table">
                        <thead class="position-sticky top-0 start-0 z-2">
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
                                            <a href="../../controller/administrador/administrador_controller.php?excluir=<?= $admin['id'] ?>"
                                                class="action-btn btn-delete"
                                                onclick="return confirm('Tem certeza que deseja excluir este administrador?')">
                                                Excluir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">Nenhum administrador cadastrado.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>