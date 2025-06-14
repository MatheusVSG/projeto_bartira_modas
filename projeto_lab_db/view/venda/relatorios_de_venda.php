<?php
// Inicia a sessão para verificar se o usuário está logado
session_start();

// Inclui o controlador responsável por gerar o relatório de vendas
require_once '../../controller/vendas/RelatorioVendasController.php';

// Conecta ao banco de dados
include_once '../../connection.php';

// Verifica se o usuário está logado e se é um administrador
// Caso não esteja, redireciona para a tela de login
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Cria uma instância do controlador do relatório de vendas
$controller = new RelatorioVendasController();

// Chama o método que gera os dados do relatório (consultas ao banco)
$dados = $controller->gerarRelatorio();

// Define botões de navegação adicionais (voltar e gerar PDF)
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

    <!-- Inclui o head padrão (Bootstrap, estilos, ícones, etc.) -->
    <?php include '../../head.php'; ?>

    <!-- Biblioteca Chart.js para gerar gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">

        <!-- Barra de navegação do sistema -->
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Relatório de Vendas por Mês</h4>

            <!-- Verifica se há dados para exibir -->
            <?php if ($dados): ?>
                <!-- Tabela com os dados do relatório -->
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
                            <!-- Percorre os dados e exibe cada linha da tabela -->
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

                <!-- Área onde o gráfico será desenhado -->
                <canvas id="graficoVendas" class="mt-4"></canvas>

                <script>
                    // Gera os rótulos do eixo X no formato "Ano-Mês"
                    const labels = <?= json_encode(array_map(fn($item) => $item['ano'] . '-' . str_pad($item['mes'], 2, '0', STR_PAD_LEFT), $dados)); ?>;

                    // Gera os dados do gráfico (valores vendidos por mês)
                    const data = <?= json_encode(array_map(fn($item) => $item['total_vendido'], $dados)); ?>;

                    // Seleciona o canvas e obtém o contexto gráfico 2D
                    const ctx = document.getElementById('graficoVendas').getContext('2d');

                    // Cria o gráfico com Chart.js
                    new Chart(ctx, {
                        type: 'line', // Tipo: linha
                        data: {
                            labels: labels, // Eixo X
                            datasets: [{
                                label: 'Total Vendido (R$)', // Legenda da linha
                                data: data, // Eixo Y
                                borderColor: 'rgba(75, 192, 192, 1)', // Cor da linha
                                backgroundColor: 'rgba(75, 192, 192, 0.1)', // Cor de fundo abaixo da linha
                                fill: true, // Preenche abaixo da linha
                                tension: 0.3 // Suaviza a curva
                            }]
                        },
                        options: {
                            responsive: true, // Responsivo (ajusta ao tamanho da tela)
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Ano-Mês' // Título do eixo X
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Valor (R$)' // Título do eixo Y
                                    },
                                    ticks: {
                                        // Formata os valores do eixo Y com "R$" e separadores brasileiros
                                        callback: value => 'R$ ' + value.toLocaleString('pt-BR')
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php else: ?>
                <!-- Caso não haja dados, exibe uma mensagem de aviso -->
                <div class="alert alert-warning mt-3">Nenhum dado encontrado para exibir.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>