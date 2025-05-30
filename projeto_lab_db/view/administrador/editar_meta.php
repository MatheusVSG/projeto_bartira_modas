<?php
session_start();
include '../../connection.php';
include '../../head.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$meta = null;
$vendedor_nome = '';

// Obter o ID da meta da URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $meta_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Buscar a meta pelo ID
    $query_meta = "SELECT m.id, m.fk_vendedor_id, m.valor, m.data_validade, v.nome as vendedor_nome
                   FROM meta_vendas m
                   JOIN vendedores v ON m.fk_vendedor_id = v.id
                   WHERE m.id = '$meta_id' LIMIT 1"; // Busca pela ID da meta
    $result_meta = mysqli_query($conn, $query_meta);

    if ($result_meta && mysqli_num_rows($result_meta) > 0) {
        $meta = mysqli_fetch_assoc($result_meta);
        $vendedor_nome = $meta['vendedor_nome'];
    } else {
        $_SESSION['error_message'] = "Meta não encontrada.";
        header("Location: metas_funcionario.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "ID da meta não especificado.";
    header("Location: metas_funcionario.php");
    exit();
}

// Processar a submissão do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meta_id = $_POST['meta_id'];
    $novo_valor = $_POST['valor'];
    $nova_data_validade = $_POST['data_validade'];
    $modificado_por = $_SESSION['usuario_id'];

    $query_update = "UPDATE meta_vendas
                     SET valor = '$novo_valor', data_validade = '$nova_data_validade', modificado_por = '$modificado_por'
                     WHERE id = '$meta_id'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success_message'] = "Meta atualizada com sucesso!";
        header("Location: metas_funcionario.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar meta: " . mysqli_error($conn);
         header("Location: metas_funcionario.php"); // Redirecionar de volta em caso de erro
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Editar Meta</title>
    <link href="../../path_to_bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-8 col-lg-7 bg-light p-4 rounded shadow position-relative">
             <a href="metas_funcionario.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar</a>
            <h2 class="text-center text-dark mb-4">Editar Meta de Vendas para <?= $vendedor_nome ?></h2>

            <?php if ($meta): ?>
                <form method="POST" action="editar_meta.php?id=<?= $meta['id'] ?>">
                    <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor da Meta:</label>
                        <input type="number" step="0.01" name="valor" id="valor" class="form-control" value="<?= $meta['valor'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_validade" class="form-label">Data de Validade:</label>
                        <input type="date" name="data_validade" id="data_validade" class="form-control" value="<?= $meta['data_validade'] ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Atualizar Meta</button>
                </form>
            <?php else: ?>
                <div class="alert alert-danger text-center">Erro ao carregar dados da meta.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php mysqli_close($conn); ?> 