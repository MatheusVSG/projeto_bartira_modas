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

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Cadastro de Estoque</title>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-8 col-md-6 col-lg-5 bg-light p-3 rounded shadow">
            <h2 class="text-center text-dark mb-3">Cadastrar Estoque</h2>

            <form action="../../controller/estoque_controller.php" method="POST">
                <div class="mb-2">
                    <label for="tamanho" class="form-label">Tamanho</label>
                    <input type="text" name="tamanho" id="tamanho" required class="form-control form-control-sm">
                </div>

                <div class="mb-2">
                    <label for="fk_produto_id" class="form-label">Produto</label>
                    <select name="fk_produto_id" id="fk_produto_id" required class="form-control form-control-sm">
                        <option value="">Selecione o produto</option>
                        <?php
                        $query = "SELECT id, nome FROM produtos";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>{$row['id']} - {$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="quantidade" class="form-label">Quantidade</label>
                    <input type="text" name="quantidade" id="quantidade" required class="form-control form-control-sm">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>
                    <button type="submit" name="cadastrar_estoque" class="btn btn-success btn-sm">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>