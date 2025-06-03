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

$linksAdicionais = [
    [
        'caminho' => '../../view/administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'metas_funcionario.php',
        'titulo' => 'Nova Meta',
        'cor' => 'btn-primary'
    ]
];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Editar Meta</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <!-- Mensagens Sucesso/Erro -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }

            if (isset($_SESSION['error_message'])) {
            ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <h4 class="text-warning">
            Editar Meta de Vendas para <?= $vendedor_nome ?>
        </h4>

        <?php if ($meta): ?>
            <div class="bg-light rounded p-4">
                <form method="POST" action="editar_meta.php?id=<?= $meta['id'] ?>" class="row">
                    <input type="hidden" name="meta_id" value="<?= $meta['id'] ?>">
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="valor" class="form-label">Valor da Meta:</label>
                        <div class="input-group">
                             <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" name="valor" id="valor" class="form-control" value="<?= $meta['valor'] ?>" required>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="data_validade" class="form-label">Data de Validade:</label>
                        <input type="date" name="data_validade" id="data_validade" class="form-control" value="<?= $meta['data_validade'] ?>" required>
                    </div>
                    <div class="col-12 d-flex justify-content-end align-items-center">
                         <button type="submit" class="btn btn-success">Atualizar Meta</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">Erro ao carregar dados da meta.</div>
        <?php endif; ?>
    </div>

    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

<?php mysqli_close($conn); ?> 