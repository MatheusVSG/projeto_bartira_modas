<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$sql = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, t.nome as tipo_nome, e.tamanho, e.quantidade
        FROM produtos p
        LEFT JOIN tipos_produto t ON p.tipo_id = t.id
        LEFT JOIN estoque e ON p.id = e.fk_produto_id";
$result = $conn->query($sql);

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_produto.php',
        'titulo' => 'Novo Produto',
        'cor' => 'btn-primary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Produtos com Estoque</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Lista de Produtos</h4>

            <div class="table-responsive mt-3 bg-light rounded shadow-sm p-3">
                <?php if ($result->num_rows > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID Produto</th>
                                <th>Nome</th>
                                <th>Valor</th>
                                <th>Tipo</th>
                                <th>Foto</th>
                                <th>Tamanho</th>
                                <th>Quantidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="ID Produto"><?= $row['produto_id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($row['produto_nome']) ?></td>
                                    <td data-label="Valor">R$ <?= number_format($row['valor_unidade'], 2, ',', '.') ?></td>
                                    <td data-label="Tipo"><?= htmlspecialchars($row['tipo_nome']) ?></td>
                                    <td data-label="Foto">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalFoto<?= $row['produto_id'] ?>">
                                            <img src="../../view/produto/fotos/<?= $row['foto'] ?>" width="50" class="rounded">
                                        </a>


                                        <div class="modal fade" id="modalFoto<?= $row['produto_id'] ?>" tabindex="-1" aria-labelledby="modalFotoLabel<?= $row['produto_id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalFotoLabel<?= $row['produto_id'] ?>">Foto do Produto</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../../view/produto/fotos/<?= $row['foto'] ?>" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Tamanho"><?= $row['tamanho'] ?? '-' ?></td>
                                    <td data-label="Quantidade"><?= $row['quantidade'] ?? '-' ?></td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_produto.php?id=<?= $row['produto_id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/produto/excluir_produto.php?id=<?= $row['produto_id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este produto e seu estoque?')" class="action-btn btn-delete">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results mt-3">Nenhum produto encontrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>