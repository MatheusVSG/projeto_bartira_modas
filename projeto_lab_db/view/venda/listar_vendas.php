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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-3">Vendas Realizadas</h2>

            <div class="mb-2 text-right">
                <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>
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
$conn->close();
?>