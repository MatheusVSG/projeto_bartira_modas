<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Apenas administradores podem editar produtos.';
    header("Location: ../../");
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'Produto não identificado.';
    header("Location: ../../view/produto/listar_produtos.php");
    exit;
}

$id = $_GET['id'];
$tamanho = $_GET['tamanho'];

$sql = "SELECT p.*, e.quantidade, e.tamanho
        FROM produtos AS p
        LEFT JOIN estoque AS e ON e.fk_produto_id = p.id
        WHERE p.id = ? AND e.tamanho = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $tamanho);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    echo "Produto não encontrado.";
    exit;
}

$produto = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Editar Produto</title>
</head>

<body>
    <div class="bg-dark text-light px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../../view/produto/cadastro-produto.php',
                'titulo' => 'Voltar ao Cadastro',
                'cor' => 'btn-secondary'
            ],
            [
                'caminho' => '../../view/produto/listar_produtos.php',
                'titulo' => 'Produtos Cadastrados',
                'cor' => 'btn-primary'
            ]
        ];

        include '../../components/barra_navegacao.php';
        ?>
        <!-- Alertas -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['warning_message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['warning_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['warning_message']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php } ?>
        </div>

        <h4 class="text-warning mb-4">Editar Produto</h4>

        <div class="bg-light text-dark p-4 rounded shadow-sm">
            <form action="../../controller/produto/atualizar_produto.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="atualizacao_completa" value="S" required>
                <input type="hidden" name="id" value="<?= $produto['id'] ?>" required>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($produto['nome']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_id" class="form-label">Tipo de Produto:</label>
                    <select name="tipo_id" id="tipo_id" required class="form-select">
                        <option value="">Selecione o tipo</option>
                        <?php
                        $tipos = $conn->query("SELECT id, nome FROM tipos_produto");
                        while ($tipo = $tipos->fetch_assoc()) {
                            $selected = ($produto['tipo_id'] == $tipo['id']) ? 'selected' : '';
                            echo "<option value='{$tipo['id']}' $selected>{$tipo['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="tamanho" class="form-label">Tamanho:</label>
                    <input type="text" name="tamanho" value="<?= $produto['tamanho'] ?>" id="tamanho" readonly required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="quantidade" class="form-label">Quantidade:</label>
                    <input type="number" name="quantidade" value="<?= $produto['quantidade'] ?>" id="quantidade" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                    <input type="text" name="valor_unidade" value="<?= $produto['valor_unidade'] ?>" id="valor_unidade" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Nova Foto (opcional):</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto Atual:</label><br>
                    <img src="./fotos/<?= $produto['foto'] ?>" width="120" class="rounded border">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>