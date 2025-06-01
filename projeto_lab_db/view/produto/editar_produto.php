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
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    echo "Produto não encontrado.";
    exit;
}

$produto = $resultado->fetch_assoc();

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

include '../../head.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Editar Produto</title>
</head>

<body class="bg-dark text-light">
    <?php include '../../components/barra_navegacao.php'; ?>

    <div class="container py-4">

        <!-- Alertas -->
        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
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
                <input type="hidden" name="id" value="<?= $produto['id'] ?>">

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($produto['nome']) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="tipo_id" class="form-label">Tipo de Produto:</label>
                    <select name="tipo_id" id="tipo_id" class="form-select" required>
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
                    <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                    <input type="number" step="0.01" name="valor_unidade" id="valor_unidade" value="<?= $produto['valor_unidade'] ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tamanhos e Quantidades:</label>
                    <div id="tamanhos-container">
                        <?php
                        $sql_tamanhos = "SELECT tamanho, quantidade FROM estoque WHERE fk_produto_id = ?";
                        $stmt_tamanhos = $conn->prepare($sql_tamanhos);
                        $stmt_tamanhos->bind_param("i", $produto['id']);
                        $stmt_tamanhos->execute();
                        $result_tamanhos = $stmt_tamanhos->get_result();

                        while ($tamanho = $result_tamanhos->fetch_assoc()) {
                            echo '<div class="input-group mb-2">
                                    <input type="text" name="tamanhos[]" class="form-control" value="' . htmlspecialchars($tamanho['tamanho']) . '" placeholder="Tamanho" required>
                                    <input type="number" name="quantidades[]" class="form-control" value="' . htmlspecialchars($tamanho['quantidade']) . '" placeholder="Qtd" min="1" required>
                                    <button type="button" class="btn btn-danger" onclick="removerTamanho(this)">Remover</button>
                                </div>';
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="adicionarTamanho()">Adicionar Tamanho</button>
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Nova Foto (opcional):</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto Atual:</label><br>
                    <img src="fotos/<?= $produto['foto'] ?>" width="120" class="rounded border">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-warning">Limpar</button>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function adicionarTamanho() {
            const container = document.getElementById('tamanhos-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" name="tamanhos[]" class="form-control" placeholder="Tamanho" required>
                <input type="number" name="quantidades[]" class="form-control" placeholder="Qtd" min="1" required>
                <button type="button" class="btn btn-danger" onclick="removerTamanho(this)">Remover</button>
            `;
            container.appendChild(div);
        }

        function removerTamanho(button) {
            button.parentElement.remove();
        }
    </script>
</body>

</html>