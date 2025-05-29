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

    $vendedor_id = $_POST['vendedor_id'];
    $meta_valor = $_POST['meta_valor'];
    $data_validade = $_POST['data_validade'];
    $modificado_por = $_SESSION['usuario_id'];

    $query = "INSERT INTO meta_vendas (fk_vendedor_id, valor, data_validade, modificado_por) 
              VALUES ('$vendedor_id', '$meta_valor', '$data_validade', '$modificado_por')";

    if (mysqli_query($conn, $query)) {
        echo "<div class='alert alert-success text-center' role='alert'>Meta cadastrada com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger text-center' role='alert'>Erro ao cadastrar meta: " . mysqli_error($conn) . "</div>";
    }
}


if ($_SESSION['tipo_usuario'] == 'admin') {
    $query_vendedores = "SELECT v.id AS vendedor_id, v.nome AS vendedor_nome, m.valor AS meta_valor, m.status AS meta_status
                         FROM vendedores v
                         LEFT JOIN meta_vendas m ON v.id = m.fk_vendedor_id
                         WHERE m.data_validade >= CURDATE()";
} else {
    $query_vendedores = "SELECT v.id AS vendedor_id, v.nome AS vendedor_nome, m.valor AS meta_valor, m.status AS meta_status
                         FROM vendedores v
                         LEFT JOIN meta_vendas m ON v.id = m.fk_vendedor_id
                         WHERE v.id = {$_SESSION['usuario_id']} AND m.data_validade >= CURDATE()";
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

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
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
                        <input type="number" step="0.01" name="meta_valor" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_validade" class="form-label">Data de Validade:</label>
                        <input type="date" name="data_validade" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cadastrar Meta</button>
                </form>
            <?php endif; ?>

            <h3 class="text-center text-dark mt-5 mb-3">Metas de Vendas</h3>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Fornecedor</th>
                        <th>Meta</th>
                        <th>Status da Meta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($vendedor = mysqli_fetch_assoc($result_vendedores)) : ?>
                        <tr>
                            <td><?= $vendedor['vendedor_nome']; ?></td>
                            <td><?= $vendedor['meta_valor']; ?></td>
                            <td>
                                <?php
                                if (isset($vendedor['meta_status'])) {
                                    echo $vendedor['meta_status'] == 1 ? "Meta atingida" : "Meta não atingida";
                                } else {
                                    echo "Meta não definida";
                                }
                                ?>
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