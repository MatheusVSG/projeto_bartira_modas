<?php
session_start();
include_once '../../connection.php';
include '../../head.php';

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


$sql = "SELECT * FROM clientes ORDER BY nome";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Lista de Clientes</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3 overflow-auto">

        <div class="col-12 col-sm-10 col-md-10 col-lg-10 bg-light p-3 rounded shadow">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-dark">Clientes</h2>
                <a href="cadastro_cliente.php" class="btn btn-primary btn-sm">+ Novo Cliente</a>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <?php if (isset($_GET['excluido']) && $_GET['excluido'] == 1): ?>
                            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                Cliente excluído com sucesso!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1): ?>
                            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                Cliente atualizado com sucesso!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                            </div>
                        <?php endif; ?>
                        <thead class="table-dark">
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
                                    <td><?= $cliente['id'] ?></td>
                                    <td><?= htmlspecialchars($cliente['nome']) ?></td>
                                    <td><?= htmlspecialchars($cliente['cpf']) ?></td>
                                    <td><?= htmlspecialchars($cliente['email']) ?></td>
                                    <td>
                                        <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <a href="../../controller/cliente/excluir_cliente.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Nenhum cliente encontrado.</div>
            <?php endif; ?>

            <?php
            if ($_SESSION['tipo_usuario'] == 'admin') {
                echo '<a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>';
            } elseif ($_SESSION['tipo_usuario'] == 'vendedor') {
                echo '<a href="../vendedor/home_vendedor.php" class="btn btn-secondary btn-sm">Voltar</a>';
            }
            ?>
        </div>
</body>

</html>