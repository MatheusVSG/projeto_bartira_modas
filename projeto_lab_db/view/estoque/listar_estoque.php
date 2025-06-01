<?php
session_start();
include '../../connection.php';

if (
    !isset($_SESSION['usuario_id']) ||
    !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])
) {
    header("Location: ../../login.php");
    exit();
}

$tipoFiltro = isset($_GET['tipo_id']) ? $_GET['tipo_id'] : '';
$nomeFiltro = isset($_GET['nome_produto']) ? $_GET['nome_produto'] : '';

$query = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, t.nome as tipo_nome, e.tamanho, e.quantidade
          FROM produtos p
          LEFT JOIN tipos_produto t ON p.tipo_id = t.id
          LEFT JOIN estoque e ON p.id = e.fk_produto_id
          WHERE 1=1";
if ($tipoFiltro) {
    $query .= " AND t.id = '" . mysqli_real_escape_string($conn, $tipoFiltro) . "'";
}
if ($nomeFiltro) {
    $query .= " AND p.nome LIKE '%" . mysqli_real_escape_string($conn, $nomeFiltro) . "%'";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Estoque</title>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],
        ];

        include '../../components/barra_navegacao.php'
        ?>

        <h4 class="text-warning">Estoque</h4>

        <div class="flex-grow-1 overflow-y-hidden d-flex flex-column">
            <form method="GET" class="d-flex gap-2 align-items-end mb-3">
                <div>
                    <label for="nome_produto" class="form-label text-light">Nome do Produto</label>
                    <input type="text" name="nome_produto" id="nome_produto" value="<?= htmlspecialchars($nomeFiltro) ?>" class="form-control form-control-sm">
                </div>

                <div>
                    <label for="tipo_id" class="form-label text-light">Tipo</label>
                    <select name="tipo_id" id="tipo_id" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <?php
                        $tipos = $conn->query("SELECT id, nome FROM tipos_produto");
                        while ($tipo = $tipos->fetch_assoc()) {
                            $selected = ($tipoFiltro == $tipo['id']) ? 'selected' : '';
                            echo "<option value='{$tipo['id']}' $selected>{$tipo['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                <a href="relatorio_estoque_pdf.php" target="_blank" class="btn btn-success btn-sm">Gerar PDF</a>
            </form>

            <div class="flex-grow-1 overflow-y-auto">
                <table class="custom-table">
                    <thead class="position-sticky top-0 start-0 z-2">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Foto</th>
                            <th>Tamanho</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['produto_id']; ?></td>
                                <td><?php echo $row['produto_nome']; ?></td>
                                <td>R$ <?php echo isset($row['valor_unidade']) ? number_format($row['valor_unidade'], 2, ',', '.') : '-'; ?></td>
                                <td><?php echo $row['tipo_nome']; ?></td>
                                <td>
                                    <?php if (!empty($row['foto'])): ?>
                                        <a href="#" data-toggle="modal" data-target="#modalFotoEstoque<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>">
                                            <img src="../produto/fotos/<?php echo $row['foto']; ?>" width="50" class="rounded">
                                        </a>

                                        <div class="modal fade" id="modalFotoEstoque<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>" tabindex="-1" role="dialog" aria-labelledby="modalFotoEstoqueLabel<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header position-relative">
                                                        <h5 class="modal-title" id="modalFotoEstoqueLabel<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>">Foto do Produto: <?php echo $row['produto_nome']; ?></h5>
                                                        <button type="button" class="btn-close position-absolute" style="right: 16px; top: 16px;" data-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../produto/fotos/<?php echo $row['foto']; ?>" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span>Sem foto</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['tamanho'] ?? '-'; ?></td>
                                <td><?php echo $row['quantidade'] ?? '-'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>