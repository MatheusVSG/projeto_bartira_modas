<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SESSION['tipo_usuario'] == 'admin') {
    $sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
            FROM vendas v
            INNER JOIN clientes c ON v.fk_cliente_id = c.id
            INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
            INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
            ORDER BY v.data_criacao DESC";
} else {
    $sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
            FROM vendas v
            INNER JOIN clientes c ON v.fk_cliente_id = c.id
            INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
            INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
            WHERE v.fk_vendedor_id = {$_SESSION['usuario_id']}
            ORDER BY v.data_criacao DESC";
}

$stmt = $conn->prepare($sql);
if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}

$result = $stmt->get_result();

$sql_meta = "SELECT valor FROM meta_vendas WHERE fk_vendedor_id = {$_SESSION['usuario_id']} AND data_validade >= CURDATE() LIMIT 1";
$stmt_meta = $conn->prepare($sql_meta);
if (!$stmt_meta->execute()) {
    die("Erro ao executar consulta de meta: " . $stmt_meta->error);
}
$result_meta = $stmt_meta->get_result();
$meta = $result_meta->fetch_assoc();

$sql_total_vendas = "SELECT SUM(valor) AS total_vendas FROM vendas WHERE fk_vendedor_id = {$_SESSION['usuario_id']}";
$stmt_total_vendas = $conn->prepare($sql_total_vendas);
if (!$stmt_total_vendas->execute()) {
    die("Erro ao calcular o total das vendas: " . $stmt_total_vendas->error);
}
$result_total_vendas = $stmt_total_vendas->get_result();
$total_vendas = $result_total_vendas->fetch_assoc()['total_vendas'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Vendas Realizadas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-3">Vendas Realizadas</h2>

            <div class="mb-2 text-right">
                <?php
                if ($_SESSION['tipo_usuario'] == 'admin') {
                    echo '<a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>';
                } elseif ($_SESSION['tipo_usuario'] == 'vendedor') {
                    echo '<a href="../vendedor/home_vendedor.php" class="btn btn-secondary btn-sm">Voltar</a>';
                }
                ?>
            </div>

            <?php if ($_SESSION['tipo_usuario'] == 'vendedor'): ?>
                <div class="alert alert-info">
                    <h5>Meta de Vendas</h5>
                    <p>Valor da Meta: R$
                        <?php

                        if (isset($meta) && isset($meta['valor'])) {
                            echo number_format($meta['valor'], 2, ',', '.');
                        } else {
                            echo 'Sem meta definida';
                        }
                        ?>
                    </p>
                    <p>Valor Total de Vendas: R$ <?= number_format($total_vendas, 2, ',', '.') ?></p>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Forma de Pagamento</th>
                            <th>Valor</th>
                            <th>Data da Venda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['cliente']}</td>
                                        <td>{$row['vendedor']}</td>
                                        <td>{$row['forma_pagto']}</td>
                                        <td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>
                                        <td>" . date("d/m/Y H:i:s", strtotime($row['data_criacao'])) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Nenhuma venda encontrada</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$stmt_meta->close();
$stmt_total_vendas->close();
$conn->close();
?>