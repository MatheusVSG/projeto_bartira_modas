<?php


require_once '../../controller/vendas/RelatorioVendasController.php';

$controller = new RelatorioVendasController();
$dados = $controller->gerarRelatorio();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Vendas por Mês</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-dark">

<div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center p-3">
    <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
        <h2 class="text-center text-dark mb-3">Relatório de Vendas por Mês</h2>

        <div class="mb-2 text-right">
            <a href="listar_vendas.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>

        <?php if ($dados): ?>
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Ano</th>
                        <th>Mês</th>
                        <th>Total Vendido (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados as $row): ?>
                        <tr>
                            <td><?= $row['ano'] ?></td>
                            <td><?= str_pad($row['mes'], 2, '0', STR_PAD_LEFT) ?></td>
                            <td>R$ <?= number_format($row['total_vendido'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <canvas id="graficoVendas" class="mt-4"></canvas>

            <script>
                const labels = <?php echo json_encode(array_map(function($item) {
                    return $item['ano'] . '-' . str_pad($item['mes'], 2, '0', STR_PAD_LEFT);
                }, $dados)); ?>;

                const data = <?php echo json_encode(array_column($dados, 'total_vendido')); ?>;

                const ctx = document.getElementById('graficoVendas').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Vendido (R$)',
                            data: data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: false,
                            tension: 0.1
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
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        }
                    }
                });
            </script>

        <?php else: ?>
            <p class="text-center">Nenhum dado encontrado para exibir.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
