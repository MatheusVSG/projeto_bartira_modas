<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para acessar esta página.';
    header("Location: ../../login.php");
    exit();
}

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'metas_funcionario.php',
        'titulo' => 'Definir Nova Meta',
        'cor' => 'btn-primary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Metas Ativas</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }
            ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <h4 class="text-warning">Metas Ativas por Vendedor</h4>

        <div>
            <?php
            $sql = "
                SELECT v.id, v.nome,
                    m.valor AS meta_valor, m.data_validade, m.data_criacao,
                    (SELECT COALESCE(SUM(valor_total), 0)
                        FROM vendas
                        WHERE fk_vendedor_id = v.id
                        AND data_venda <= m.data_validade) AS progresso_atual
                FROM vendedores v
                JOIN (
                    SELECT fk_vendedor_id, valor, data_validade, data_criacao
                    FROM meta_vendas
                    WHERE (fk_vendedor_id, data_validade) IN (
                        SELECT fk_vendedor_id, MAX(data_validade)
                        FROM meta_vendas
                        GROUP BY fk_vendedor_id
                    )
                ) m ON v.id = m.fk_vendedor_id
                WHERE v.tipo = 'vendedor'
            ";

            $res = mysqli_query($conn, $sql);

            if ($res === false) {
                die("Erro na consulta SQL: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($res) > 0): ?>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                <th>Meta R$</th>
                                <th>Progresso Atual R$</th>
                                <th>Data de Validade</th>
                                <th>Última Alteração</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td data-label="Vendedor"><?= $row['nome'] ?></td>
                                    <td data-label="Meta R$">R$ <?= number_format($row['meta_valor'], 2, ',', '.') ?></td>
                                    <td data-label="Progresso Atual R$">
                                        R$ <?= number_format($row['progresso_atual'], 2, ',', '.') ?>
                                        (<?= number_format(($row['progresso_atual'] / $row['meta_valor']) * 100, 1) ?>%)
                                    </td>
                                    <td data-label="Data de Validade"><?= date('d/m/Y', strtotime($row['data_validade'])) ?></td>
                                    <td data-label="Última Alteração"><?= date('d/m/Y H:i', strtotime($row['data_criacao'])) ?></td>
                                    <td data-label="Ação">

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">Nenhuma meta ativa encontrada.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>