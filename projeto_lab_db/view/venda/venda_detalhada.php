<?php
session_start(); // Inicia a sessão PHP para gerenciar o estado do usuário
include '../../connection.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php"); // Redireciona para a página de login se não for admin
    exit();
}

// Verifica se o ID da venda foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listar_vendas.php");
    exit();
}

$venda_id = $_GET['id'];

// Constrói a consulta SQL para obter detalhes da venda específica
$sql = "SELECT v.id AS venda_id, v.valor AS valor_total, v.data_criacao AS data_venda,
               c.nome AS cliente_nome, c.telefone AS cliente_telefone, c.email AS cliente_email,
               ve.nome AS vendedor_nome, ve.telefone AS vendedor_telefone, ve.email AS vendedor_email,
               fp.descricao AS forma_pagamento
        FROM vendas v
        INNER JOIN clientes c ON v.fk_cliente_id = c.id
        INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
        INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
        WHERE v.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venda_id);
$stmt->execute();
$result = $stmt->get_result();

// Array de links adicionais para a barra de navegação
$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'listar_vendas.php',
        'titulo' => 'Voltar às vendas',
        'cor' => 'btn-primary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?> <!-- Inclui o cabeçalho HTML padrão (meta tags, CSS, etc.) -->
    <title>Bartira Modas | Detalhes da Venda</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?> <!-- Inclui a barra de navegação -->

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Detalhes da Venda</h4>

            <div class="table-responsive">
                <?php if ($result->num_rows > 0): ?>
                    <?php $row = $result->fetch_assoc(); ?>
                    <div class="card bg-secondary text-light mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Venda #<?= $row['venda_id'] ?></h5>
                            <button type="button" class="btn btn-success" onclick="gerarRelatorio(<?= $row['venda_id'] ?>)">Gerar Relatório</button>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Vendedor:</strong> <?= htmlspecialchars($row['vendedor_nome']) ?> (<?= htmlspecialchars($row['vendedor_email']) ?>)</div>
                                <div class="col-md-6"><strong>Cliente:</strong> <?= htmlspecialchars($row['cliente_nome']) ?> (<?= htmlspecialchars($row['cliente_email']) ?>)</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Telefone do Vendedor:</strong> <?= htmlspecialchars($row['vendedor_telefone']) ?></div>
                                <div class="col-md-6"><strong>Telefone do Cliente:</strong> <?= htmlspecialchars($row['cliente_telefone']) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Valor Total:</strong> R$ <?= number_format($row['valor_total'], 2, ',', '.') ?></div>
                                <div class="col-md-6"><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($row['forma_pagamento']) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12"><strong>Data da Venda:</strong> <?= date("d/m/Y H:i:s", strtotime($row['data_venda'])) ?></div>
                            </div>

                            <h6 class="mt-3 mb-2">Itens Vendidos:</h6>
                            <ul class="list-group list-group-flush">
                                <?php
                                $sql_itens = "SELECT p.nome, t.nome as tipo_nome, iv.qtd_vendida
                                              FROM item_venda iv
                                              INNER JOIN produtos p ON iv.fk_produto_id = p.id
                                              LEFT JOIN tipos_produto t ON p.tipo_id = t.id
                                              WHERE iv.fk_venda_id = ?";
                                $stmt_itens = $conn->prepare($sql_itens);
                                $stmt_itens->bind_param("i", $venda_id);
                                $stmt_itens->execute();
                                $res_itens = $stmt_itens->get_result();
                                if ($res_itens->num_rows > 0) {
                                    while ($item = $res_itens->fetch_assoc()) {
                                        echo '<li class="list-group-item bg-dark text-light">' .
                                             htmlspecialchars($item['nome']) .
                                             ($item['tipo_nome'] ? ' (' . htmlspecialchars($item['tipo_nome']) . ')' : '') .
                                             ' - Qtd: ' . htmlspecialchars($item['qtd_vendida']) .
                                             '</li>';
                                    }
                                } else {
                                    echo '<li class="list-group-item bg-dark text-light">Nenhum item encontrado para esta venda.</li>';
                                }
                                $stmt_itens->close();
                                ?>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-results text-light">Venda não encontrada.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function gerarRelatorio(vendaId) {
            window.location.href = 'relatorio_venda_especifica.php?id=' + vendaId;
        }
    </script>
</body>

</html>

<?php
$stmt->close(); // Fecha o statement principal
$conn->close(); // Fecha a conexão com o banco de dados
?>