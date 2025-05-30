<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (
    !isset($_SESSION['usuario_id']) ||
    !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])
) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['tipo_usuario'] == 'admin') {

    if (isset($_POST['cadastrar'])) {
        $vendedor_id = $_POST['vendedor_id'];
        $meta_valor = $_POST['meta_valor'];
        $data_validade = $_POST['data_validade'];
        $modificado_por = $_SESSION['usuario_id'];

        $query = "INSERT INTO meta_vendas (fk_vendedor_id, valor, data_validade, modificado_por) 
                  VALUES ('$vendedor_id', '$meta_valor', '$data_validade', '$modificado_por')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Meta cadastrada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar meta: " . mysqli_error($conn);
        }
        header("Location: metas_funcionario.php");
        exit;
    }

    if (isset($_POST['excluir_meta'])) {
        $meta_id = $_POST['vendedor_id'];

        $query = "DELETE FROM meta_vendas WHERE id = '$meta_id'";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Meta excluída com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao excluir meta: " . mysqli_error($conn);
        }
        header("Location: metas_funcionario.php");
        exit;
    }
}

if ($_SESSION['tipo_usuario'] == 'admin') {
    $query_vendedores = "SELECT m.id AS meta_id, v.nome AS vendedor_nome, m.valor AS meta_valor, m.data_validade, SUM(vd.valor_total) AS total_vendas
                         FROM meta_vendas m
                         JOIN vendedores v ON m.fk_vendedor_id = v.id
                         LEFT JOIN vendas vd ON v.id = vd.fk_vendedor_id AND vd.data_venda BETWEEN m.data_criacao AND m.data_validade
                         WHERE m.data_validade >= CURDATE()
                         GROUP BY m.id";
} else {
    $query_vendedores = "SELECT m.id AS meta_id, v.nome AS vendedor_nome, m.valor AS meta_valor, m.data_validade, SUM(vd.valor_total) AS total_vendas
                         FROM meta_vendas m
                         JOIN vendedores v ON m.fk_vendedor_id = v.id
                         LEFT JOIN vendas vd ON v.id = vd.fk_vendedor_id AND vd.data_venda BETWEEN m.data_criacao AND m.data_validade
                         WHERE m.fk_vendedor_id = {$_SESSION['usuario_id']} AND m.data_validade >= CURDATE()
                         GROUP BY m.id";
}

$result_vendedores = mysqli_query($conn, $query_vendedores);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Definir Metas</title>
    <link href="../../path_to_bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">

    <?php
    if (isset($_SESSION['success_message'])) {
    ?>
        <div class="position-absolute top-0 start-0 pt-3 ps-3" style="z-index: 1050;">
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <?= $_SESSION['success_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
    ?>
        <div class="position-absolute top-0 start-0 pt-3 ps-3" style="z-index: 1050;">
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                <?= $_SESSION['error_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php
        unset($_SESSION['error_message']);
    }
    ?>

    <div class="w-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 bg-light p-4 rounded shadow position-relative">
            <a href="home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
            <div class="d-flex justify-content-end mb-2 gap-2">
                <!-- Botão de voltar já está fixo acima -->
            </div>
            <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
                <h2 class="text-center text-dark mb-4">Definir Meta de Vendas</h2>
                <form method="POST" action="metas_funcionario.php">
                    <div class="mb-3">
                        <label for="vendedor_id" class="form-label">Fornecedor:</label>
                        <select name="vendedor_id" id="vendedor_id" class="form-select" required>
                            <?php
                            $vendedores_result = mysqli_query($conn, "SELECT id, nome FROM vendedores");
                            while ($vendedor = mysqli_fetch_assoc($vendedores_result)) {
                                echo "<option value='{$vendedor['id']}'>{$vendedor['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="meta_valor" class="form-label">Valor da Meta:</label>
                        <input type="number" step="0.01" name="meta_valor" class="form-control" id="meta_valor" placeholder="Valor da meta" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_validade" class="form-label">Data de Validade:</label>
                        <input type="date" name="data_validade" class="form-control" required>
                    </div>
                    <button type="submit" name="cadastrar" class="btn btn-primary w-100">Cadastrar Meta</button>
                </form>
            <?php endif; ?>

            <h3 class="text-center text-dark mt-5 mb-3">Metas de Vendas</h3>
            <table class="table table-striped table-bordered">
                <thead class="table-dark" style="display: table-header-group;">
                    <tr>
                        <th>Fornecedor</th>
                        <th>Meta</th>
                        <th>Data de Validade</th>
                        <th>Status da Meta</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($vendedor = mysqli_fetch_assoc($result_vendedores)) : ?>
                        <tr>
                            <td><?= $vendedor['vendedor_nome']; ?></td>
                            <td><?= $vendedor['meta_valor']; ?></td>
                            <td><?= date('d/m/Y', strtotime($vendedor['data_validade'])); ?></td>
                            <td>
                                <?php
                                $meta_valor = $vendedor['meta_valor'];
                                $total_vendas = $vendedor['total_vendas'] ?? 0;
                                $porcentagem = ($meta_valor > 0) ? round(($total_vendas / $meta_valor) * 100, 2) : 0;
                                $porcentagem_display = number_format($porcentagem, 2) . '%';

                                // Definir a cor da barra de progresso
                                $progress_class = 'bg-danger'; // Vermelho por padrão (0-25%)
                                if ($porcentagem > 25 && $porcentagem <= 50) {
                                    $progress_class = 'bg-warning'; // Laranja (25-50%)
                                } elseif ($porcentagem > 50 && $porcentagem <= 75) {
                                    $progress_class = 'bg-info'; // Azul (50-75%)
                                } elseif ($porcentagem > 75) {
                                    $progress_class = 'bg-success'; // Verde (75%+) e 100%
                                }

                                ?>
                                <?php if ($meta_valor > 0): ?>
                                    <div class="progress">
                                        <div class="progress-bar <?= $progress_class ?>" role="progressbar" style="width: <?= min(100, $porcentagem) ?>%;" aria-valuenow="<?= $porcentagem ?>" aria-valuemin="0" aria-valuemax="100">

                                        </div>
                                    </div>
                                    <span class="ms-2"><?= $porcentagem_display ?></span>
                                <?php else: ?>
                                    Meta não definida
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
                                    <a href="editar_meta.php?id=<?= $vendedor['meta_id'] ?>" class="btn btn-warning btn-sm me-2">Editar</a>
                                    <form method="POST" action="metas_funcionario.php" style="display:inline;">
                                        <input type="hidden" name="excluir_meta">
                                        <input type="hidden" name="vendedor_id" value="<?= $vendedor['meta_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta meta?\')">Excluir</button>
                                    </form>
                                <?php else: ?>
                                    <span>Sem permissão</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="mb-4 text-start">
                <!-- Removido botão de voltar -->
            </div>
        </div>
    </div>

    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php mysqli_close($conn); ?>