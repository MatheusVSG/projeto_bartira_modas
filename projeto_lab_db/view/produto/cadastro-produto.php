<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../");
    exit();
}

$sql = "SELECT p.id, p.nome, p.valor_unidade FROM produtos AS p ORDER BY p.nome ASC";
$produtos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Produto</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../administrador/home_adm.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],
            [
                'caminho' => 'listar_estoque.php',
                'titulo' => 'Produtos Cadastrados',
                'cor' => 'btn-primary',
            ]
        ];
        include_once '../../components/barra_navegacao.php';
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

        <h4 class="text-warning mb-4">Cadastro de Produto</h4>

        <div class="bg-light rounded p-4">
            <form action="../../controller/produto/salvar_produto.php" method="POST" class="row">
                <div class="form-group mb-3">
                    <label for="produto_id" class="form-label">Selecione um Produto ou Cadastre um Novo:</label>
                    <select name="produto_id" id="produto_id" required class="form-select">
                        <option value="novo_produto">-- Cadastrar Novo Produto --</option>

                        <?php while ($produto = $produtos->fetch_assoc()): ?>
                            <option value="<?php echo $produto['id']; ?>">
                                <?php
                                echo "#{$produto['id']} " . "- " . htmlspecialchars($produto['nome'])
                                    . " R$" . number_format($produto['valor_unidade'], 2, ',', '.');
                                ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div id="campos_novo_produto" class="mb-3">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome do Produto:</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="tipo_id" class="form-label">Tipo de Produto:</label>
                        <select name="tipo_id" id="tipo_id" class="form-control">
                            <option value="">Selecione o tipo</option>
                            <?php
                            $tipos = $conn->query("SELECT id, nome FROM tipos_produto");
                            while ($tipo = $tipos->fetch_assoc()) {
                                echo "<option value='{$tipo['id']}'>{$tipo['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                        <input type="number" name="valor_unidade" id="valor_unidade" step="0.01" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label for="foto" class="form-label">Foto do Produto:</label>
                        <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Tamanhos e Quantidades:</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text">Tamanho</span>
                        <input type="text" name="tamanho" class="form-control" placeholder="(P, M, G, 38, 40)">
                        <span class="input-group-text">Quantidade</span>
                        <input type="number" name="quantidade" min="1" required dir="rtl" placeholder="100" class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Salvar Produto</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript para mostrar/esconder os campos de novo produto
        const produtoSelect = document.getElementById('produto_id');
        const camposNovoProduto = document.getElementById('campos_novo_produto');
        const nomeNovoProdutoInput = document.getElementById('nome');
        const tipoNovoProduto = document.getElementById('tipo_id')
        const valorNovoProdutoInput = document.getElementById('valor_unidade');

        produtoSelect.addEventListener('change', function() {
            if (this.value === 'novo_produto') {
                camposNovoProduto.style.display = 'block';
                // Tornar campos de novo produto obrigatórios
                nomeNovoProdutoInput.required = true;
                tipoNovoProduto.required = true;
                valorNovoProdutoInput.required = true;
            } else {
                camposNovoProduto.style.display = 'none';
                // Remover obrigatoriedade
                nomeNovoProdutoInput.required = false;
                tipoNovoProduto.required = false;
                valorNovoProdutoInput.required = false;
            }
        });
    </script>
</body>

</html>