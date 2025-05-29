<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
        FROM vendas v
        INNER JOIN clientes c ON v.fk_cliente_id = c.id
        INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
        INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
        ORDER BY v.data_criacao DESC";

$stmt = $conn->prepare($sql);

if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Vendas Realizadas</title>
    <style>
        html, body {
            background-color: #222 !important;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-3">Vendas Realizadas</h2>

            <div class="d-flex justify-content-end mb-2 gap-2">
                <a href="relatorios_de_venda.php" class="btn btn-primary btn-sm">Relatório de Vendas</a>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Forma de Pagamento</th>
                            <th>Valor</th>
                            <th>Produtos Vendidos</th>
                            <th>Data da Venda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Buscar produtos vendidos para esta venda
                                $produtos = [];
                                $sql_prod = "SELECT p.nome, t.nome as tipo_nome, iv.qtd_vendida FROM item_venda iv
                                             INNER JOIN produtos p ON iv.fk_produto_id = p.id
                                             LEFT JOIN tipos_produto t ON p.tipo_id = t.id
                                             WHERE iv.fk_venda_id = " . $row['id'];
                                $res_prod = $conn->query($sql_prod);
                                while ($prod = $res_prod->fetch_assoc()) {
                                    $produtos[] = $prod['nome'] .
                                        ($prod['tipo_nome'] ? ' (' . $prod['tipo_nome'] . ')' : '') .
                                        ' - Qtd: ' . $prod['qtd_vendida'];
                                }
                                $produtos_str = implode('<br>', $produtos);
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['cliente']}</td>
                                        <td>{$row['vendedor']}</td>
                                        <td>{$row['forma_pagto']}</td>
                                        <td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>
                                        <td>{$produtos_str}</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>