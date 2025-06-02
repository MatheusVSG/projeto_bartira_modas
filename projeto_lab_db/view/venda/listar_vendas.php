<?php
session_start();
include '../../connection.php';

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

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'relatorios_de_venda.php',
        'titulo' => 'Relatório de Vendas',
        'cor' => 'btn-primary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Vendas Realizadas</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Vendas Realizadas</h4>

            <div class="table-responsive">
                <?php if ($result->num_rows > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Forma de Pagamento</th>
                                <th>Valor</th>
                                <th>Produtos Vendidos</th>
                                <th>Data da Venda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $produtos = [];
                                $sql_prod = "SELECT p.nome, t.nome as tipo_nome, iv.qtd_vendida
                                                 FROM item_venda iv
                                                 INNER JOIN produtos p ON iv.fk_produto_id = p.id
                                                 LEFT JOIN tipos_produto t ON p.tipo_id = t.id
                                                 WHERE iv.fk_venda_id = ?";
                                $stmt_prod = $conn->prepare($sql_prod);
                                $stmt_prod->bind_param("i", $row['id']);
                                $stmt_prod->execute();
                                $res_prod = $stmt_prod->get_result();
                                while ($prod = $res_prod->fetch_assoc()) {
                                    $produtos[] = $prod['nome'] .
                                        ($prod['tipo_nome'] ? ' (' . $prod['tipo_nome'] . ')' : '') .
                                        ' - Qtd: ' . $prod['qtd_vendida'];
                                }
                                $produtos_str = implode('<br>', $produtos);
                                ?>
                                <tr>
                                    <td data-label="ID"><?= $row['id'] ?></td>
                                    <td data-label="Cliente"><?= htmlspecialchars($row['cliente']) ?></td>
                                    <td data-label="Vendedor"><?= htmlspecialchars($row['vendedor']) ?></td>
                                    <td data-label="Forma de Pagamento"><?= htmlspecialchars($row['forma_pagto']) ?></td>
                                    <td data-label="Valor">R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                                    <td data-label="Produtos Vendidos"><?= $produtos_str ?></td>
                                    <td data-label="Data da Venda"><?= date("d/m/Y H:i:s", strtotime($row['data_criacao'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhuma venda registrada.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>