<?php
session_start(); // Inicia a sessão PHP para gerenciar o estado do usuário
include '../../connection.php'; // Inclui o arquivo de conexão com o banco de dados

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php"); // Redireciona para a página de login se não for admin
    exit();
}

// Constrói a consulta SQL base para buscar as vendas com informações de cliente, vendedor e forma de pagamento
$sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
        FROM vendas v
        INNER JOIN clientes c ON v.fk_cliente_id = c.id
        INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
        INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
        WHERE 1=1"; // 'WHERE 1=1' é uma cláusula que permite adicionar facilmente condições AND posteriormente

$params = []; // Array para armazenar os parâmetros a serem vinculados (valores dos filtros)
$types = ""; // String para armazenar os tipos dos parâmetros (ex: 's' para string, 'i' para integer)

// Lógica para aplicar o filtro por data da venda
if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio'])) {
    $data_inicio = $_GET['data_inicio'];
    $sql .= " AND DATE(v.data_criacao) >= ?";
    $params[] = $data_inicio;
    $types .= "s";
}

if (isset($_GET['data_fim']) && !empty($_GET['data_fim'])) {
    $data_fim = $_GET['data_fim'];
    $sql .= " AND DATE(v.data_criacao) <= ?";
    $params[] = $data_fim;
    $types .= "s";
}

// Lógica para aplicar o filtro por nome do vendedor
if (isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {
    $vendedor = $_GET['vendedor'];
    $sql .= " AND ve.nome LIKE ?"; // Adiciona condição para filtrar por nome de vendedor (LIKE para busca parcial)
    $params[] = "%$vendedor%"; // Adiciona wildcards para busca parcial do nome
    $types .= "s"; // Tipo string para o nome do vendedor
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
        'caminho' => 'relatorios_de_venda.php',
        'titulo' => 'Relatório de Vendas',
        'cor' => 'btn-primary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?> <!-- Inclui o cabeçalho HTML padrão (meta tags, CSS, etc.) -->
    <title>Bartira Modas | Vendas Realizadas</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?> <!-- Inclui a barra de navegação -->

        <!-- Alertas -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['warning_message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['warning_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['warning_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php } ?>
        </div>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Vendas Realizadas</h4>

            <!-- Formulário de Filtros -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="data_inicio" class="text-light">Data Inicial:</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="data_fim" class="text-light">Data Final:</label>
                            <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= isset($_GET['data_fim']) ? $_GET['data_fim'] : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="vendedor" class="text-light">Vendedor:</label>
                            <input type="text" class="form-control" id="vendedor" name="vendedor" value="<?= isset($_GET['vendedor']) ? htmlspecialchars($_GET['vendedor']) : '' ?>" placeholder="Nome do vendedor" oninput="this.value = this.value.replace(/^[ ]+|[0-9]/g, '');">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="listar_vendas.php" class="btn btn-secondary">Limpar Filtros</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive"> <!-- Container responsivo para a tabela -->
                <?php if ($result->num_rows > 0): ?> <!-- Verifica se há resultados para exibir -->
                    <table class="custom-table"> <!-- Início da tabela de vendas -->
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Forma de Pagamento</th>
                                <th>Valor</th>
                                <th>Detalhes da Venda</th> <!-- Nova coluna para o botão de detalhes -->
                                <th>Data da Venda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?> <!-- Loop para exibir cada venda como uma linha da tabela -->
                                <?php
                                // A lógica para $produtos e $produtos_str foi movida para venda_detalhada.php
                                ?>
                                <tr>
                                    <td data-label="ID"><?= $row['id'] ?></td>
                                    <td data-label="Cliente"><?= htmlspecialchars($row['cliente']) ?></td>
                                    <td data-label="Vendedor"><?= htmlspecialchars($row['vendedor']) ?></td>
                                    <td data-label="Forma de Pagamento"><?= htmlspecialchars($row['forma_pagto']) ?></td>
                                    <td data-label="Valor">R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                                    <td data-label="Detalhes">
                                        <!-- Botão para ver os detalhes da venda, redireciona para venda_detalhada.php com o ID da venda -->
                                        <a href="venda_detalhada.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Ver Detalhes</a>
                                    </td>
                                    <td data-label="Data da Venda"><?= date("d/m/Y H:i:s", strtotime($row['data_criacao'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhuma venda registrada.</div> <!-- Mensagem se não houver vendas ou resultados de filtro -->
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>

<?php
$stmt->close(); // Fecha o statement preparado
$conn->close(); // Fecha a conexão com o banco de dados
?>