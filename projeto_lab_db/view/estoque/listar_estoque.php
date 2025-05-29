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

$query = "SELECT e.*, p.nome as produto_nome, t.nome as tipo_nome FROM estoque e
          LEFT JOIN produtos p ON e.fk_produto_id = p.id
          LEFT JOIN tipos_produto t ON p.tipo_id = t.id
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

            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Tamanho</th>
                        <th>Produto ID</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Data de Modificação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['tamanho']; ?></td>
                            <td><?php echo $row['fk_produto_id']; ?></td>
                            <td><?php echo $row['produto_nome']; ?></td>
                            <td><?php echo $row['tipo_nome']; ?></td>
                            <td><?php echo $row['quantidade']; ?></td>
                            <td><?php echo $row['data_de_modificacao']; ?></td>
                            <td>
                                <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>

                                    <form method="POST" action="../../controller/estoque_controller.php" style="display:inline;">
                                        <input type="hidden" name="tamanho" value="<?php echo $row['tamanho']; ?>">
                                        <input type="hidden" name="fk_produto_id" value="<?php echo $row['fk_produto_id']; ?>">
                                        <button type="submit" name="excluir_estoque" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                <?php else: ?>

                                    <span>Sem permissão</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>