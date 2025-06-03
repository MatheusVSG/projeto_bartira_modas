<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (
    !isset($_SESSION['usuario_id']) ||
    !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])
) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['tipo_usuario'] == 'admin') {
    if (isset($_POST['cadastrar_meta'])) {
        $vendedor_id = $_POST['vendedor_id'];
        $meta_valor = $_POST['meta_valor'];
        $data_validade = $_POST['data_validade'];
        $modificado_por = $_SESSION['usuario_id'];

        $query = "INSERT INTO meta_vendas (fk_vendedor_id, valor, data_validade, modificado_por) 
                  VALUES ('$vendedor_id', '$meta_valor', '$data_validade', '$modificado_por')";

        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Meta cadastrada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar meta: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['excluir_meta'])) {
        $vendedor_id = $_POST['vendedor_id'];
        // Adicionar lógica para excluir a meta mais recente ou uma meta específica se houver ID
        // Por enquanto, vou excluir a meta mais recente para o vendedor
        $query = "DELETE FROM meta_vendas WHERE fk_vendedor_id = '$vendedor_id' ORDER BY data_validade DESC LIMIT 1";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = "Meta excluída com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao excluir meta: " . mysqli_error($conn);
        }
    }
    header("Location: metas_funcionario.php");
    exit();
}

// Buscar metas de vendas e calcular o total de vendas do mês para cada vendedor com meta válida
$vendedores_com_meta = [];
$query_metas = "SELECT v.id AS vendedor_id, v.nome AS vendedor_nome, m.id AS meta_id, m.valor AS meta_valor,
                     m.data_validade, m.data_criacao
                     FROM vendedores v
                     INNER JOIN meta_vendas m ON v.id = m.fk_vendedor_id
                     WHERE m.data_validade >= CURDATE()
                     ORDER BY v.nome, m.data_validade DESC";
$result_metas = mysqli_query($conn, $query_metas);

if ($result_metas) {
    while ($meta = mysqli_fetch_assoc($result_metas)) {
        $vendedor_id = $meta['vendedor_id'];
        
        // Buscar total de vendas do mês para este vendedor
        $sql_total_vendas = "SELECT SUM(valor) AS total_vendas 
                             FROM vendas 
                             WHERE fk_vendedor_id = {$vendedor_id}
                             AND MONTH(data_venda) = MONTH(CURRENT_DATE())
                             AND YEAR(data_venda) = YEAR(CURRENT_DATE())";
        $result_total_vendas = mysqli_query($conn, $sql_total_vendas);
        $total_vendas = mysqli_fetch_assoc($result_total_vendas)['total_vendas'] ?? 0;

        $meta['total_vendas_mes'] = $total_vendas;
        $vendedores_com_meta[] = $meta;
    }
}

// Buscar vendedores sem meta válida (apenas para admin)
$vendedores_sem_meta = [];
if ($_SESSION['tipo_usuario'] == 'admin') {
    $query_sem_metas = "SELECT v.id AS vendedor_id, v.nome AS vendedor_nome
                        FROM vendedores v
                        LEFT JOIN meta_vendas m ON v.id = m.fk_vendedor_id AND m.data_validade >= CURDATE()
                        WHERE m.id IS NULL AND v.tipo = 'vendedor'
                        ORDER BY v.nome";
    $result_sem_metas = mysqli_query($conn, $query_sem_metas);
    if ($result_sem_metas) {
         while ($vendedor = mysqli_fetch_assoc($result_sem_metas)) {
            $vendedores_sem_meta[] = $vendedor;
        }
    }
}

$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? 'home_adm.php' : '../vendedor/home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Definir Metas</title>
    <style>
        .progress-bar-text {
            color: #000;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <!-- Mensagens Sucesso/Erro -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }

            if (isset($_SESSION['error_message'])) {
            ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
            <h4 class="text-warning">Definir Nova Meta</h4>
            <div class="bg-light rounded p-4 mb-4">
                <form method="POST" action="metas_funcionario.php" class="row">
                     <input type="hidden" name="cadastrar_meta">
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="vendedor_id" class="form-label">Vendedor:</label>
                        <select name="vendedor_id" id="vendedor_id" class="form-select" required>
                            <option value="">Selecione um vendedor</option>
                            <?php
                             // Combinar vendedores com meta e sem meta para a lista de seleção
                            $todos_vendedores_result = mysqli_query($conn, "SELECT id, nome FROM vendedores WHERE tipo = 'vendedor' ORDER BY nome");
                            while ($vendedor = mysqli_fetch_assoc($todos_vendedores_result)) {
                                echo "<option value='{$vendedor['id']}'>{$vendedor['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="meta_valor" class="form-label">Valor da Meta:</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" name="meta_valor" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="data_validade" class="form-label">Data de Validade:</label>
                        <input type="date" name="data_validade" class="form-control" required>
                    </div>
                    <div class="col-12 d-flex justify-content-end align-items-center gap-2">
                         <button type="reset" class="btn btn-warning">
                            Limpar
                        </button>
                        <button type="submit" class="btn btn-success">Cadastrar Meta</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <h4 class="text-warning mb-3">Lista de Metas Ativas</h4>
        <div class="bg-light rounded p-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Vendedor</th>
                            <th>Meta</th>
                            <th>Vendas no Mês</th>
                            <th>Progresso</th>
                            <th>Data de Validade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($vendedores_com_meta) > 0): ?>
                            <?php foreach ($vendedores_com_meta as $meta_info) : ?>
                                <tr>
                                    <td><?= $meta_info['vendedor_nome']; ?></td>
                                    <td>R$ <?= number_format($meta_info['meta_valor'], 2, ',', '.'); ?></td>
                                    <td>R$ <?= number_format($meta_info['total_vendas_mes'], 2, ',', '.'); ?></td>
                                    <td>
                                        <?php 
                                        $percentual = ($meta_info['total_vendas_mes'] / $meta_info['meta_valor']) * 100;
                                        $percentual = min($percentual, 100);
                                        $cor = $percentual >= 100 ? 'success' : ($percentual >= 70 ? 'warning' : 'danger');
                                        ?>
                                        <?php if ($percentual >= 100): ?>
                                            <span class="badge bg-success">Meta batida!</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning"><?= number_format($percentual, 1) ?>%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($meta_info['data_validade'])); ?></td>
                                    <td>
                                        <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
                                             <a href="editar_meta.php?id=<?= $meta_info['meta_id'] ?>" class="btn btn-warning btn-sm me-2">Editar</a>
                                            <form method="POST" action="metas_funcionario.php" style="display:inline;">
                                                <input type="hidden" name="excluir_meta">
                                                <input type="hidden" name="vendedor_id" value="<?= $meta_info['vendedor_id'] ?>">
                                                 <?php // Adicionar campo para identificar qual meta excluir se houver multiplas ativas no futuro ?>
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta meta?')">Excluir</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Sem permissão</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma meta ativa encontrada.</td>
                            </tr>
                        <?php endif; ?>
                         <?php if ($_SESSION['tipo_usuario'] == 'admin' && count($vendedores_sem_meta) > 0): ?>
                              <tr>
                                <td colspan="6" class="text-center table-info">Vendedores sem meta ativa: 
                                     <?= implode(', ', array_column($vendedores_sem_meta, 'vendedor_nome')) ?>
                                </td>
                             </tr>
                         <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

<?php mysqli_close($conn); ?>