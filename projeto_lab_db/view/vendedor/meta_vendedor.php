<?php
session_start(); // Inicia a sessão para controle de login
include '../../connection.php'; // Inclui o arquivo com a conexão ao banco de dados
include '../../head.php'; // Inclui o cabeçalho com os metadados e links CSS/JS

// Verifica se o usuário está logado e se é admin ou vendedor
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php"); // Redireciona para o login se não estiver autorizado
    exit();
}

// Consulta para administradores: lista todas as vendas
if ($_SESSION['tipo_usuario'] == 'admin') {
    $sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
            FROM vendas v
            INNER JOIN clientes c ON v.fk_cliente_id = c.id
            INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
            INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
            ORDER BY v.data_criacao DESC";
} else {
    // Consulta para vendedores: lista apenas as vendas feitas pelo vendedor logado
    $sql = "SELECT v.id, c.nome AS cliente, ve.nome AS vendedor, fp.descricao AS forma_pagto, v.valor, v.data_criacao
            FROM vendas v
            INNER JOIN clientes c ON v.fk_cliente_id = c.id
            INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
            INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
            WHERE v.fk_vendedor_id = {$_SESSION['usuario_id']}
            ORDER BY v.data_criacao DESC";
}

// Prepara e executa a consulta das vendas
$stmt = $conn->prepare($sql);
if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}
$result = $stmt->get_result(); // Armazena o resultado

// Consulta para pegar a meta do vendedor (se for vendedor)
$sql_meta = "SELECT valor, data_validade FROM meta_vendas 
             WHERE fk_vendedor_id = {$_SESSION['usuario_id']} 
             AND data_validade >= CURDATE() 
             ORDER BY data_validade DESC 
             LIMIT 1";
$stmt_meta = $conn->prepare($sql_meta);
if (!$stmt_meta->execute()) {
    die("Erro ao executar consulta de meta: " . $stmt_meta->error);
}
$result_meta = $stmt_meta->get_result();
$meta = $result_meta->fetch_assoc(); // Armazena a meta mais recente válida

$total_vendas = 0;

// Consulta o total de vendas do ano atual para o vendedor
$sql_total_vendas = "SELECT SUM(valor) AS total_vendas 
                     FROM vendas 
                     WHERE fk_vendedor_id = ?
                     AND YEAR(data_criacao) = YEAR(CURRENT_DATE())";
$stmt_total_vendas = $conn->prepare($sql_total_vendas);
$stmt_total_vendas->bind_param("i", $_SESSION['usuario_id']);
if (!$stmt_total_vendas->execute()) {
    die("Erro ao calcular o total das vendas: " . $stmt_total_vendas->error);
}
$result_total_vendas = $stmt_total_vendas->get_result();
$row_total_vendas = $result_total_vendas->fetch_assoc();
$total_vendas = $row_total_vendas['total_vendas'] ?? 0; // Armazena total ou 0

// Define links adicionais para navegação
$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : 'home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => '../venda/cadastrar_venda.php',
        'titulo' => 'Cadastrar Venda',
        'cor' => 'btn-primary'
    ]
];
?>

<!-- HTML da página -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../../head.php'; ?> <!-- Inclui o head novamente -->
    <title>Bartira Modas | Vendas Realizadas</title>
    <style>
        .progress {
            height: 25px;
            /* Altura da barra de progresso */
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?> <!-- Barra de navegação -->

        <h4 class="text-warning">Vendas Realizadas</h4>

        <!-- Se o usuário for vendedor, mostra a meta -->
        <?php if ($_SESSION['tipo_usuario'] == 'vendedor'): ?>
            <div class="card bg-white mb-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Meta de Vendas</h5>

                    <!-- Exibe meta se existir -->
                    <?php if (isset($meta) && isset($meta['valor'])): ?>
                        <div class="mb-3">
                            <p class="text-dark mb-1">Valor da Meta: R$ <?= number_format($meta['valor'], 2, ',', '.') ?></p>
                            <p class="text-dark mb-1">Data de Validade: <?= date('d/m/Y', strtotime($meta['data_validade'])) ?></p>
                            <p class="text-dark mb-3">Valor Total de Vendas: R$ <?= number_format($total_vendas, 2, ',', '.') ?></p>

                            <?php
                            // Calcula o percentual da meta alcançada
                            $percentual = ($total_vendas / $meta['valor']) * 100;
                            $percentual = min($percentual, 100); // Limita a 100%
                            $cor = $percentual >= 100 ? 'success' : ($percentual >= 70 ? 'warning' : 'danger'); // Define cor da barra
                            ?>

                            <!-- Barra de progresso da meta -->
                            <div class="progress mb-2">
                                <div class="progress-bar bg-<?= $cor ?>" role="progressbar"
                                    style="width: <?= $percentual ?>%"
                                    aria-valuenow="<?= $percentual ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    <?= number_format($percentual, 1) ?>%
                                </div>
                            </div>

                            <!-- Mensagem se atingiu ou não a meta -->
                            <p class="text-dark mb-0">
                                <?php if ($percentual >= 100): ?>
                                    <span class="badge bg-success">Meta atingida!</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Faltam R$ <?= number_format($meta['valor'] - $total_vendas, 2, ',', '.') ?> para atingir a meta</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Caso não exista meta -->
                        <p class="text-dark">Nenhuma meta definida para o período atual.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabela de vendas -->
        <div class="bg-light rounded p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
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
                        // Exibe as vendas se houver registros
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
                            // Mensagem se não houver vendas
                            echo "<tr><td colspan='6' class='text-center'>Nenhuma venda encontrada</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script do Bootstrap -->
    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

<?php
// Fecha os statements e conexão com o banco
$stmt->close();
$stmt_meta->close();
$stmt_total_vendas->close();
$conn->close();
?>