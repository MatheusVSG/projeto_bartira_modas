<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['sucesso'])) {
    $mensagem_sucesso = match($_GET['sucesso']) {
        '1' => 'Meta cadastrada com sucesso!',
        '2' => 'Meta atualizada com sucesso!',
        '3' => 'Meta excluída com sucesso!',
        default => ''
    };
}

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'metas_funcionario.php',
        'titulo' => 'Nova Meta',
        'cor' => 'btn-primary'
    ]
];

// Primeiro buscamos todos os vendedores
$sql_vendedores = "SELECT id, nome FROM vendedores WHERE tipo = 'vendedor' ORDER BY nome";
$vendedores = mysqli_query($conn, $sql_vendedores);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Metas por Vendedor</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">
                Metas por Vendedor
            </h4>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive mt-3">
                <?php if (mysqli_num_rows($vendedores) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th>Meta Atual</th>
                                <th>Vendas (R$)</th>
                                <th>% Atingido</th>
                                <th>Validade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendedor = mysqli_fetch_assoc($vendedores)): 
                                // Busca a meta mais recente do vendedor
                                $sql_meta = "SELECT valor, data_validade 
                                           FROM meta_vendas 
                                           WHERE fk_vendedor_id = ? 
                                           ORDER BY data_validade DESC 
                                           LIMIT 1";
                                $stmt_meta = $conn->prepare($sql_meta);
                                $stmt_meta->bind_param("i", $vendedor['id']);
                                $stmt_meta->execute();
                                $meta = $stmt_meta->get_result()->fetch_assoc();
                                
                                // Busca o total de vendas do vendedor
                                $sql_vendas = "SELECT COALESCE(SUM(valor), 0) as total 
                                             FROM vendas 
                                             WHERE fk_vendedor_id = ?";
                                $stmt_vendas = $conn->prepare($sql_vendas);
                                $stmt_vendas->bind_param("i", $vendedor['id']);
                                $stmt_vendas->execute();
                                $total_vendas = $stmt_vendas->get_result()->fetch_assoc()['total'];
                                
                                // Calcula o percentual atingido
                                $percentual = ($meta && $meta['valor'] > 0) ? ($total_vendas / $meta['valor']) * 100 : 0;
                                $cor_progresso = $percentual >= 100 ? 'bg-success' : ($percentual >= 70 ? 'bg-warning' : 'bg-danger');
                            ?>
                                <tr>
                                    <td data-label="Vendedor"><?= htmlspecialchars($vendedor['nome']) ?></td>
                                    <td data-label="Meta Atual">
                                        <?= $meta ? 'R$ ' . number_format($meta['valor'], 2, ',', '.') : 'Sem meta' ?>
                                    </td>
                                    <td data-label="Vendas (R$)">R$ <?= number_format($total_vendas, 2, ',', '.') ?></td>
                                    <td data-label="% Atingido">
                                        <?php if ($meta): ?>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar <?= $cor_progresso ?>" 
                                                     role="progressbar" 
                                                     style="width: <?= min($percentual, 100) ?>%" 
                                                     aria-valuenow="<?= $percentual ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <?= number_format($percentual, 1) ?>%
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Validade">
                                        <?= $meta ? date('d/m/Y', strtotime($meta['data_validade'])) : '-' ?>
                                    </td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="metas_funcionario.php?vendedor_id=<?= $vendedor['id'] ?>" class="action-btn btn-edit">Gerenciar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhum vendedor cadastrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>