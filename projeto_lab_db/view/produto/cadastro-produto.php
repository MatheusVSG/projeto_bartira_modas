<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $mensagem_sucesso = 'Produto cadastrado com sucesso!';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Produto</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
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
                'caminho' => 'listar_produtos.php',
                'titulo' => 'Produtos Cadastrados',
                'cor' => 'btn-primary',
            ]
        ];
        include '../../../components/barra_navegacao.php';
        ?>

        <h4 class="text-warning mb-0">Cadastro de Produto</h4>

        <div class="bg-light rounded p-4 mt-3">
            <?php if (!empty($mensagem_sucesso)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $mensagem_sucesso ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <form action="../../controller/produto/salvar_produto.php" method="post" enctype="multipart/form-data" class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Produto:</label>
                    <input type="text" name="nome" id="nome" class="form-control" required>
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="tipo_id" class="form-label">Tipo de Produto:</label>
                    <select name="tipo_id" id="tipo_id" class="form-control" required>
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

                <div class="col-12 mb-3">
                    <label class="form-label">Tamanhos e Quantidades:</label>
                    <div id="tamanhos-container">
                        <div class="input-group mb-2">
                            <input type="text" name="tamanhos[]" class="form-control" placeholder="Tamanho" required>
                            <input type="number" name="quantidades[]" class="form-control" placeholder="Qtd" min="1" required>
                            <button type="button" class="btn btn-danger" onclick="removerTamanho(this)">Remover</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" onclick="adicionarTamanho()">Adicionar Tamanho</button>
                </div>

                <div class="col-12 mb-3">
                    <label for="foto" class="form-label">Foto do Produto:</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Salvar Produto</button>
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