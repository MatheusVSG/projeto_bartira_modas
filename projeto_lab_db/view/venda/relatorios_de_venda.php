<?php
session_start();
require_once '../../controller/vendas/RelatorioVendasController.php';
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$controller = new RelatorioVendasController();
$dados = $controller->gerarRelatorio();

$linksAdicionais = [
    [
        'caminho' => 'listar_vendas.php',
        'titulo' => 'Voltar às Vendas',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'relatorio_vendas_pdf.php',
        'titulo' => 'Gerar PDF',
        'cor' => 'btn-success'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Relatório de Vendas por Mês</title>
    <?php include '../../head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Relatório de Vendas por Mês</h4>

            <?php if ($dados): ?>
                <div class="table-responsive mt-3">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Ano</th>
                                <th>Mês</th>
                                <th>Total Vendido (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $row): ?>
                                <tr>
                                    <td data-label="Ano"><?= $row['ano'] ?></td>
                                    <td data-label="Mês"><?= str_pad($row['mes'], 2, '0', STR_PAD_LEFT) ?></td>
                                    <td data-label="Total Vendido">R$ <?= number_format($row['total_vendido'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <canvas id="graficoVendas" class="mt-4"></canvas>

                <script>
                    const labels = <?= json_encode(array_map(fn($item) => $item['ano'] . '-' . str_pad($item['mes'], 2, '0', STR_PAD_LEFT), $dados)); ?>;
                    const data = <?= json_encode(array_map(fn($item) => $item['total_vendido'], $dados)); ?>;

                    const ctx = document.getElementById('graficoVendas').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Vendido (R$)',
                                data: data,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Ano-Mês'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Valor (R$)'
                                    },
                                    ticks: {
                                        callback: value => 'R$ ' + value.toLocaleString('pt-BR')
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php else: ?>
                <div class="alert alert-warning mt-3">Nenhum dado encontrado para exibir.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>