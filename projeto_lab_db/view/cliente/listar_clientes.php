<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['excluido']) && $_GET['excluido'] == 1) {
    $mensagem_sucesso = 'Cliente excluído com sucesso!';
} elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1) {
    $mensagem_sucesso = 'Cliente atualizado com sucesso!';
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

        <h4 class="text-warning mb-0">
            Lista de Clientes
        </h4>

        <?php if (!empty($mensagem_sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= htmlspecialchars($mensagem_sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

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