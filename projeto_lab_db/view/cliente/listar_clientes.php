<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para realizar esta ação.';
    header("Location: ../../");
    exit();
}

$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_cliente.php',
        'titulo' => 'Novo Cliente',
        'cor' => 'btn-primary'
    ]
];

$sql = "SELECT * FROM clientes ORDER BY nome";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Lista de Clientes</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <!-- Mensagens Sucesso/Erro -->
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

        <h4 class="text-warning mb-0">
            Lista de Clientes
        </h4>

        <div class="mt-3">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cliente = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $cliente['id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($cliente['nome']) ?></td>
                                    <td data-label="CPF"><?= htmlspecialchars($cliente['cpf']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($cliente['email']) ?></td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/cliente/excluir_cliente.php?id=<?= $cliente['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">Nenhum cliente encontrado.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>