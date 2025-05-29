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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>

</head>

<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="d-flex justify-content-end mb-2 gap-2">
            <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
        </div>

        <h1 class="text-center text-warning mb-5">Cadastro de Produto</h1>

        <form action="../../controller/produto/salvar_produto.php" method="post" enctype="multipart/form-data" class="bg-light text-dark p-4 rounded shadow-sm mb-5">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Produto:</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>

            <div class="mb-3">
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

            <div class="mb-3">
                <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                <input type="number" name="valor_unidade" id="valor_unidade" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tamanhos e Quantidades:</label>
                <div id="tamanhos-container">
                    <div class="input-group mb-2">
                        <input type="text" name="tamanhos[]" class="form-control" placeholder="Tamanho" required>
                        <input type="number" name="quantidades[]" class="form-control" placeholder="Qtd" min="1" required>
                        <button type="button" class="btn btn-danger" onclick="removerTamanho(this)">Remover</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="adicionarTamanho()">Adicionar Tamanho</button>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Foto do Produto:</label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-success">Salvar Produto</button>
        </form>

        <h2 class="text-center text-light mb-4">Lista de Produtos e Estoque</h2>

        <div class="table-responsive bg-light rounded shadow-sm">
            <table class="table table-bordered table-hover text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID Produto</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Foto</th>
                        <th>Tamanho</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include_once '../../connection.php';
                    // Modificar a query para incluir informações de estoque
                    $sql = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, t.nome as tipo_nome, e.tamanho, e.quantidade
                            FROM produtos p
                            LEFT JOIN tipos_produto t ON p.tipo_id = t.id
                            LEFT JOIN estoque e ON p.id = e.fk_produto_id";
                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['produto_id']}</td>
                                <td>{$row['produto_nome']}</td>
                                <td>R$ " . number_format($row['valor_unidade'], 2, ',', '.') . "</td>
                                <td>{$row['tipo_nome']}</td>
                                <td>
                                    <a href='#' data-toggle='modal' data-target='#modalFotoProdutoCad{$row['produto_id']}'>
                                        <img src='../../view/produto/fotos/{$row['foto']}' width='50' class='rounded'>
                                    </a>

                                    <!-- Modal -->
                                    <div class='modal fade' id='modalFotoProdutoCad{$row['produto_id']}' tabindex='-1' role='dialog' aria-labelledby='modalFotoProdutoCadLabel{$row['produto_id']}' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered' role='document'>
                                        <div class='modal-content'>
                                        <div class='modal-header position-relative'>
                                            <h5 class='modal-title' id='modalFotoProdutoCadLabel{$row['produto_id']}'>Foto do Produto: {$row['produto_nome']}</h5>
                                            <button type='button' class='btn-close position-absolute' style='right: 16px; top: 16px;' data-dismiss='modal' aria-label='Fechar'></button>
                                        </div>
                                        <div class='modal-body text-center'>
                                            <img src='../../view/produto/fotos/{$row['foto']}' class='img-fluid rounded'>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </td>
                                <td>" . (isset($row['tamanho']) ? $row['tamanho'] : '-') . "</td>
                                <td>" . (isset($row['quantidade']) ? $row['quantidade'] : '-') . "</td>
                                <td>
                                    <a href='editar_produto.php?id={$row['produto_id']}' class='btn btn-sm btn-warning'>Editar</a>
                                    <a href='/projeto_lab_db/controller/produto/excluir_produto.php?id={$row['produto_id']}' onclick='return confirm(\"Tem certeza que deseja excluir este produto e todo o seu estoque relacionado?\"')' class='btn btn-sm btn-danger'>Excluir Produto</a>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
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