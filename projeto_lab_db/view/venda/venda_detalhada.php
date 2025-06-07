<?php
session_start(); // Inicia a sessão PHP para gerenciar o estado do usuário
include '../../connection.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php"); // Redireciona para a página de login se não for admin
    exit();
}

// Obtém os valores dos filtros de data inicial e final da URL, se existirem
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Constrói a consulta SQL base para obter detalhes da venda, incluindo informações de cliente, vendedor e forma de pagamento
$sql = "SELECT v.id AS venda_id, v.valor AS valor_total, v.data_criacao AS data_venda,
               c.nome AS cliente_nome, c.telefone AS cliente_telefone, c.email AS cliente_email,
               ve.nome AS vendedor_nome, ve.telefone AS vendedor_telefone, ve.email AS vendedor_email,
               fp.descricao AS forma_pagamento
        FROM vendas v
        INNER JOIN clientes c ON v.fk_cliente_id = c.id
        INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
        INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
        WHERE 1=1"; // 'WHERE 1=1' permite adicionar facilmente condições AND posteriormente

$params = []; // Array para armazenar os parâmetros a serem vinculados (valores dos filtros)
$types = ""; // String para armazenar os tipos dos parâmetros (ex: 's' para string)

// Lógica para aplicar o filtro por data inicial
if (!empty($data_inicio)) {
    $sql .= " AND DATE(v.data_criacao) >= ?"; // Adiciona condição para vendas a partir da data inicial
    $params[] = $data_inicio;
    $types .= "s"; // Tipo string para a data
}

// Lógica para aplicar o filtro por data final
if (!empty($data_fim)) {
    $sql .= " AND DATE(v.data_criacao) <= ?"; // Adiciona condição para vendas até a data final
    $params[] = $data_fim;
    $types .= "s"; // Tipo string para a data
}

$sql .= " ORDER BY v.data_criacao DESC"; // Ordena os resultados pela data de criação da venda (mais recente primeiro)

$stmt = $conn->prepare($sql); // Prepara a consulta SQL para execução segura contra injeção SQL

// Verifica se a preparação da consulta falhou
if ($stmt === false) {
    die("Erro ao preparar consulta: " . $conn->error); // Exibe erro e encerra se a preparação falhar
}

// Vincula os parâmetros à consulta preparada, se houver filtros aplicados
if (!empty($params)) {
    $stmt->bind_param($types, ...$params); // Vincula os parâmetros dinamicamente
}

// Executa a consulta preparada
if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error); // Exibe erro e encerra se a execução falhar
}

$result = $stmt->get_result(); // Obtém o conjunto de resultados da consulta

// Array de links adicionais para a barra de navegação/botões de ação
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
    <title>Bartira Modas | Vendas Detalhadas</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?> <!-- Inclui a barra de navegação -->

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Vendas Detalhadas</h4>

            <!-- Formulário de Filtros por Data -->
            <form method="GET" class="mb-3"> <!-- Formulário para aplicar os filtros de data, usa método GET -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_inicio" class="text-light">Data Inicial:</label>
                            <!-- Campo de input para data inicial, com valor pré-preenchido se já filtrado -->
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_fim" class="text-light">Data Final:</label>
                            <!-- Campo de input para data final, com valor pré-preenchido se já filtrado -->
                            <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button> <!-- Botão para submeter o formulário de filtro -->
                        <a href="venda_detalhada.php" class="btn btn-secondary">Limpar Filtros</a> <!-- Botão para limpar os filtros, recarrega a página sem parâmetros -->
                        <button type="button" class="btn btn-success ms-2" onclick="gerarRelatorio()">Gerar Relatório</button> <!-- Botão para gerar relatório, chama função JS -->
                    </div>
                </div>
            </form>

            <div class="table-responsive"> <!-- Container para exibir os detalhes das vendas (usando cards) -->
                <?php if ($result->num_rows > 0): ?> <!-- Verifica se há resultados para exibir -->
                    <?php while ($row = $result->fetch_assoc()): ?> <!-- Loop para exibir os detalhes de cada venda -->
                        <div class="card bg-secondary text-light mb-3"> <!-- Card para cada venda -->
                            <div class="card-header">
                                <h5 class="card-title mb-0">Venda #<?= $row['venda_id'] ?></h5>
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
                                <ul class="list-group list-group-flush"> <!-- Lista de itens vendidos -->
                                    <?php
                                    // Consulta SQL para buscar os itens de cada venda
                                    $sql_itens = "SELECT p.nome, t.nome as tipo_nome, iv.qtd_vendida
                                                  FROM item_venda iv
                                                  INNER JOIN produtos p ON iv.fk_produto_id = p.id
                                                  LEFT JOIN tipos_produto t ON p.tipo_id = t.id
                                                  WHERE iv.fk_venda_id = ?";
                                    $stmt_itens = $conn->prepare($sql_itens); // Prepara a consulta de itens
                                    $stmt_itens->bind_param("i", $row['venda_id']); // Vincula o ID da venda
                                    $stmt_itens->execute(); // Executa a consulta de itens
                                    $res_itens = $stmt_itens->get_result(); // Obtém os resultados dos itens
                                    if ($res_itens->num_rows > 0) {
                                        while ($item = $res_itens->fetch_assoc()) { // Loop para exibir cada item
                                            echo '<li class="list-group-item bg-dark text-light">' .
                                                 htmlspecialchars($item['nome']) .
                                                 ($item['tipo_nome'] ? ' (' . htmlspecialchars($item['tipo_nome']) . ')' : '') .
                                                 ' - Qtd: ' . htmlspecialchars($item['qtd_vendida']) .
                                                 '</li>';
                                        }
                                    } else {
                                        echo '<li class="list-group-item bg-dark text-light">Nenhum item encontrado para esta venda.</li>'; // Mensagem se não houver itens
                                    }
                                    $stmt_itens->close(); // Fecha o statement de itens
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results text-light">Nenhuma venda encontrada para os filtros selecionados.</div> <!-- Mensagem se não houver vendas ou resultados de filtro -->
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function gerarRelatorio() {
            // Redireciona para a página de relatórios de venda
            window.location.href = 'relatorios_de_venda.php';
        }
    </script>
</body>

</html>

<?php
$stmt->close(); // Fecha o statement principal
$conn->close(); // Fecha a conexão com o banco de dados
?>