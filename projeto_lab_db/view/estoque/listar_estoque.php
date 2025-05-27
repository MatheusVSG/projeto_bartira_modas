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

$query = "SELECT * FROM estoque";
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
        <div class="col-12 col-sm-10 col-md-8 col-lg-7 bg-light p-3 rounded shadow">
            <h2 class="text-center text-dark mb-4">Estoque</h2>

            <?php
            if ($_SESSION['tipo_usuario'] == 'admin') {
                echo '<a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>';
            } elseif ($_SESSION['tipo_usuario'] == 'vendedor') {
                echo '<a href="../vendedor/home_vendedor.php" class="btn btn-secondary btn-sm">Voltar</a>';
            }
            ?>



            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Tamanho</th>
                        <th>Produto ID</th>
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