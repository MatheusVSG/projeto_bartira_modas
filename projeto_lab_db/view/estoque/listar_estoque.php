<?php
session_start();
if (
    !isset($_SESSION['usuario_id'])
    || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])
) {
    header("Location: ../../login.php");
    exit();
}

include '../../connection.php';

$tipoFiltro = isset($_GET['tipo_id']) ? $_GET['tipo_id'] : '';
$nomeFiltro = isset($_GET['nome_produto']) ? $_GET['nome_produto'] : '';

$query = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, p.tipo_id, t.nome as tipo_nome, e.tamanho, e.quantidade
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

        if ($_SESSION['tipo_usuario'] == 'admin') {
            array_push($linksAdicionais, [
                'caminho' => '../produto/cadastro-produto.php',
                'titulo' => 'Cadastrar Produto',
                'cor' => 'btn-primary',
            ]);
        }

        include '../../components/barra_navegacao.php'
        ?>

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

        <h4 class="text-warning">Estoque</h4>

        <div class="flex-grow-1 overflow-y-hidden d-flex flex-column">
            <form method="GET" class="d-flex gap-2 align-items-end mb-3">
                <div>
                    <label for="nome_produto" class="form-label text-light">Nome do Produto</label>
                    <input type="text" name="nome_produto" id="nome_produto" value="<?= htmlspecialchars($nomeFiltro) ?>" class="form-control">
                </div>

                <div>
                    <label for="tipo_id" class="form-label text-light">Tipo</label>
                    <select name="tipo_id" id="tipo_id" class="form-select">
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

                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="relatorio_estoque_pdf.php" target="_blank" class="btn btn-success">Gerar PDF</a>
            </form>

            <div class="flex-grow-1 table-responsive">
                <table class="h-100 overflow-y-auto custom-table">
                    <thead class="position-sticky top-0 start-0 z-2">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Foto</th>
                            <th>Tamanho</th>
                            <th>Quantidade</th>
                            <?php if ($_SESSION['tipo_usuario'] == 'admin') {
                            ?>
                                <th></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            // Se admin
                            if ($_SESSION['tipo_usuario'] == 'admin') {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <form action="../../controller/produto/atualizar_produto.php" method="POST">
                                        <tr>
                                            <td>
                                                <input type="text" name="id" value="<?php echo $row['produto_id']; ?>" readonly required class="form-control">
                                            </td>

                                            <td>
                                                <input type="text" value="<?php echo $row['produto_nome']; ?>" readonly class="form-control">
                                            </td>

                                            <td>
                                                <input type="text" name="valor_unidade" value="<?php echo number_format($row['valor_unidade'], 2, ',', '.'); ?>" required class="form-control">
                                            </td>

                                            <td>
                                                <input value="<?php echo $row['tipo_nome']; ?>" readonly class="form-control">
                                            </td>

                                            <td>
                                                <?php if (!empty($row['foto'])): ?>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalFotoEstoque<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>">
                                                        <img src="../produto/fotos/<?php echo $row['foto']; ?>" width="50" class="rounded">
                                                    </a>

                                                <?php else: ?>
                                                    <span>Sem foto</span>
                                                <?php endif; ?>
                                            </td>

                                            <div class="modal fade" id="modalFotoEstoque<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>" tabindex="-1" aria-labelledby="modalFotoEstoqueLabel<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header position-relative">
                                                            <h5 class="modal-title" id="modalFotoEstoqueLabel<?php echo $row['produto_id'] . str_replace(' ', '', $row['tamanho']); ?>">Foto do Produto: <?php echo $row['produto_nome']; ?></h5>
                                                            <button type="button" class="btn-close position-absolute" style="right: 16px; top: 16px;" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="../produto/fotos/<?php echo $row['foto']; ?>" class="img-fluid rounded" style="width: 100%; height: auto">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <td>
                                                <input type="text" name="tamanho" value="<?php echo $row['tamanho'] ?? ''; ?>" readonly required class="form-control">
                                            </td>

                                            <td>
                                                <input type="number" name="quantidade" value="<?php echo $row['quantidade']; ?>" class="form-control">
                                            </td>

                                            <td>
                                                <button type="submit" class="action-btn btn btn-success mb-2">Salvar</button>
                                                <a href="../produto/editar_produto.php?id=<?php echo $row['produto_id']; ?>&tamanho=<?php echo $row['tamanho']; ?>" class="action-btn btn-edit">Editar</a>
                                            </td>
                                        </tr>
                                    </form>
                                <?php }
                            }
                            // Se Vendedor
                            else {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
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
                            <?php }
                            }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum produto encontrado.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>