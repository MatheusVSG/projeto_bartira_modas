<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Apenas administradores podem acessar esta página.';
    header("Location: ../../login.php");
    exit();
}

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
    <div class="w-100 vh-100 d-flex flex-column bg-dark px-3 pb-3">
        <?php
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
            Lista de Vendedores
        </h4>

        <div class="flex-grow-1 overflow-y-hidden">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="h-100 overflow-y-auto table-responsive">
                    <table class="custom-table">
                        <thead class="position-sticky top-0 start-0 z-2">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
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
                </div>
            <?php else: ?>
                <div class="no-results">Nenhum vendedor cadastrado.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>