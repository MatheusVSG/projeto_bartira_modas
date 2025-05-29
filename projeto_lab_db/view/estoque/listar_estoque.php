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
    <title>Bartira Modas | Estoque</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-8 col-lg-7 bg-light p-3 rounded shadow position-relative">
            <div class="d-flex justify-content-end mb-2 gap-2">
                <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
            </div>
            <h2 class="text-center text-dark mb-4">Estoque</h2>

            <form method="GET" class="mb-3 d-flex gap-2 align-items-end">
                <div>
                    <label for="nome_produto" class="form-label mb-0">Nome do Produto</label>
                    <input type="text" name="nome_produto" id="nome_produto" value="<?= htmlspecialchars($nomeFiltro) ?>" class="form-control form-control-sm">
                </div>
                <div>
                    <label for="tipo_id" class="form-label mb-0">Tipo</label>
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
            </form>

            <table class="table table-bordered table-hover text-center align-middle mb-0">
                <thead class="table-dark">
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
                                    <!-- Modal -->
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
</body>

</html>