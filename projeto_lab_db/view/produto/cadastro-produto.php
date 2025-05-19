<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <?php
    session_start();
    include '../../connection.php';
    include '../../head.php';

    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {

        header("Location: ../../login.php");
        exit();
    }
    ?>

    <title>Cadastro de Produto</title>
</head>

<body class="bg-dark text-light">
    <div class="container py-5">

        <h1 class="text-center text-warning mb-5">Cadastro de Produto</h1>

        <form action="../../controller/produto/salvar_produto.php" method="post" enctype="multipart/form-data" class="bg-light text-dark p-4 rounded shadow-sm mb-5">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto:</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                <input type="number" name="valor_unidade" id="valor_unidade" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto do Produto:</label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-success">Salvar Produto</button>
        </form>

        <h2 class="text-center text-light mb-4">Lista de Produtos</h2>

        <div class="table-responsive bg-light rounded shadow-sm">
            <table class="table table-bordered table-hover text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Foto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include_once '../../connection.php';
                    $sql = "SELECT * FROM produtos";
                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['nome']}</td>
                                <td>R$ " . number_format($row['valor_unidade'], 2, ',', '.') . "</td>
                                <td><img src='../../view/produto/fotos/{$row['foto']}' width='50' class='rounded'></td>
                                <td>
                                    <a href='editar_produto.php?id={$row['id']}' class='btn btn-sm btn-warning'>Editar</a>
                                    <a href='/projeto_lab_db/controller/produto/excluir_produto.php?id={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir?\")' class='btn btn-sm btn-danger'>Excluir</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <a href="../administrador/home_adm.php" class="btn btn-secondary mt-4">Voltar ao Painel</a>
    </div>
</body>

</html>