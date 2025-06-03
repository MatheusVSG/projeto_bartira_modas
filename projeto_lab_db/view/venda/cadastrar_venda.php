<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

// Buscar meta atual do vendedor
$sql_meta = "SELECT valor, data_validade FROM meta_vendas 
             WHERE fk_vendedor_id = {$_SESSION['usuario_id']} 
             AND data_validade >= CURDATE() 
             ORDER BY data_validade DESC 
             LIMIT 1";
$stmt_meta = $conn->prepare($sql_meta);
if (!$stmt_meta->execute()) {
    die("Erro ao executar consulta de meta: " . $stmt_meta->error);
}
$result_meta = $stmt_meta->get_result();
$meta = $result_meta->fetch_assoc();

// Buscar total de vendas do mês
$sql_total_vendas = "SELECT SUM(valor) AS total_vendas 
                     FROM vendas 
                     WHERE fk_vendedor_id = {$_SESSION['usuario_id']}
                     AND MONTH(data_venda) = MONTH(CURRENT_DATE())
                     AND YEAR(data_venda) = YEAR(CURRENT_DATE())";
$stmt_total_vendas = $conn->prepare($sql_total_vendas);
if (!$stmt_total_vendas->execute()) {
    die("Erro ao calcular o total das vendas: " . $stmt_total_vendas->error);
}
$result_total_vendas = $stmt_total_vendas->get_result();
$total_vendas = $result_total_vendas->fetch_assoc()['total_vendas'] ?? 0;

// Buscar clientes
$query_clientes = "SELECT id, nome FROM clientes ORDER BY nome";
$result_clientes = mysqli_query($conn, $query_clientes);

// Buscar formas de pagamento
$query_formas = "SELECT id, descricao FROM forma_pagto ORDER BY descricao";
$result_formas = mysqli_query($conn, $query_formas);

// Buscar produtos (removido inclusão de tamanho na query)
$query_produtos = "SELECT p.id, p.nome, p.valor_unidade, e.quantidade 
                   FROM produtos p 
                   LEFT JOIN estoque e ON p.id = e.fk_produto_id 
                   WHERE e.quantidade > 0 
                   ORDER BY p.nome";
$result_produtos = mysqli_query($conn, $query_produtos);

$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    // Removido o link para 'Vendas Realizadas'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastrar Venda</title>
    <style>
        .progress {
            height: 25px;
        }
    </style>
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
            ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
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
            Cadastrar Venda
        </h4>

        <div class="bg-light rounded p-4">
            <form method="POST" action="../../controller/vendas/venda_controller.php" class="row">
                <input type="hidden" name="fk_vendedor_id" value="<?= $_SESSION['usuario_id'] ?>">
                
                <div class="col-12 col-lg-6 mb-3">
                    <label for="fk_cliente_id" class="form-label">Cliente:</label>
                    <select name="fk_cliente_id" id="fk_cliente_id" class="form-select" required>
                        <option value="">Selecione um cliente</option>
                        <?php while ($cliente = mysqli_fetch_assoc($result_clientes)) : ?>
                            <option value="<?= $cliente['id'] ?>"><?= $cliente['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="fk_forma_pagto_id" class="form-label">Forma de Pagamento:</label>
                    <select name="fk_forma_pagto_id" id="fk_forma_pagto_id" class="form-select" required>
                        <option value="">Selecione uma forma de pagamento</option>
                        <?php while ($forma = mysqli_fetch_assoc($result_formas)) : ?>
                            <option value="<?= $forma['id'] ?>"><?= $forma['descricao'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Produtos:</label>
                    <div id="produtos-container">
                        <div class="row mb-2" data-index="0">
                            <div class="col-md-6"> <?php // Ajustado o tamanho ?>
                                <select name="produtos[0][id]" class="form-select produto-select" required>
                                    <option value="">Selecione um produto</option>
                                    <?php // Reset result pointer to reuse for the first product select
mysqli_data_seek($result_produtos, 0);
                                    while ($produto = mysqli_fetch_assoc($result_produtos)) : ?>
                                        <option value="<?= $produto['id'] ?>" 
                                                data-valor="<?= $produto['valor_unidade'] ?>"
                                                data-quantidade="<?= $produto['quantidade'] ?>">
                                            <?= $produto['nome'] ?> - R$ <?= number_format($produto['valor_unidade'], 2, ',', '.') ?> 
                                            (Estoque: <?= $produto['quantidade'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6"> <?php // Ajustado o tamanho ?>
                                <input type="number" name="produtos[0][quantidade]" class="form-control quantidade-input" 
                                       placeholder="Quantidade" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <button type="button" class="btn btn-secondary btn-sm" id="adicionar-produto">Adicionar Produto</button>
                        <button type="button" class="btn btn-danger btn-sm" id="remover-produto" style="display: none;">Remover Produto</button>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Valor Total:</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" id="valor_total" name="valor" class="form-control" readonly>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="reset" class="btn btn-warning">
                        Limpar
                    </button>

                    <button type="submit" name="cadastrar_venda" class="btn btn-success">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../path_to_bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produtosContainer = document.getElementById('produtos-container');
            const adicionarProdutoBtn = document.getElementById('adicionar-produto');
            let produtoCount = 1;

            function atualizarValorTotal() {
                let total = 0;
                document.querySelectorAll('.produto-select').forEach((select, index) => {
                    const quantidade = document.querySelector(`input[name="produtos[${index}][quantidade]"]`).value;
                    const valor = select.options[select.selectedIndex].dataset.valor;
                    if (quantidade && valor) {
                        total += parseFloat(valor) * parseInt(quantidade);
                    }
                });
                document.getElementById('valor_total').value = total.toFixed(2);
            }

            function adicionarProduto() {
                const novoProduto = document.createElement('div');
                novoProduto.className = 'row mb-2';
                novoProduto.setAttribute('data-index', produtoCount);
                novoProduto.innerHTML = `
                    <div class="col-md-6"> <?php // Ajustado o tamanho ?>
                        <select name="produtos[${produtoCount}][id]" class="form-select produto-select" required>
                            <option value="">Selecione um produto</option>
                            ${document.querySelector('.produto-select').innerHTML}
                        </select>
                    </div>
                    <div class="col-md-6"> <?php // Ajustado o tamanho ?>
                        <input type="number" name="produtos[${produtoCount}][quantidade]" class="form-control quantidade-input" 
                               placeholder="Quantidade" min="1" required>
                    </div>
                `;
                produtosContainer.appendChild(novoProduto);
                produtoCount++;

                novoProduto.querySelector('.produto-select').addEventListener('change', atualizarValorTotal);
                novoProduto.querySelector('.quantidade-input').addEventListener('input', atualizarValorTotal);

                // Mostrar o botão de remover se houver mais de um produto
                if (produtosContainer.children.length > 1) {
                    document.getElementById('remover-produto').style.display = 'inline-block';
                }
            }

            function removerProduto() {
                // Remover a última linha de produto
                if (produtosContainer.children.length > 1) {
                    produtosContainer.lastElementChild.remove();
                    atualizarValorTotal();
                    reindexarProdutos();

                    // Ocultar o botão de remover se sobrar apenas um produto
                    if (produtosContainer.children.length === 1) {
                        document.getElementById('remover-produto').style.display = 'none';
                    }
                }
            }

            function reindexarProdutos() {
                produtosContainer.querySelectorAll('.row.mb-2').forEach((row, index) => {
                    row.setAttribute('data-index', index);
                    row.querySelector('.produto-select').name = `produtos[${index}][id]`;
                    row.querySelector('.quantidade-input').name = `produtos[${index}][quantidade]`;
                });
                produtoCount = produtosContainer.children.length;
            }

            adicionarProdutoBtn.addEventListener('click', adicionarProduto);
            
            // Adicionar listener ao botão remover
            document.getElementById('remover-produto').addEventListener('click', removerProduto);

            document.querySelectorAll('.produto-select').forEach(select => {
                select.addEventListener('change', atualizarValorTotal);
            });

            document.querySelectorAll('.quantidade-input').forEach(input => {
                input.addEventListener('input', atualizarValorTotal);
            });

            // Ocultar o botão de remover inicialmente se houver apenas um produto
             if (produtosContainer.children.length === 1) {
                 document.getElementById('remover-produto').style.display = 'none';
             }
        });
    </script>
</body>

</html>

<?php
mysqli_close($conn);
?>