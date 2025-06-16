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
                     AND MONTH(data_criacao) = MONTH(CURRENT_DATE())
                     AND YEAR(data_criacao) = YEAR(CURRENT_DATE())";
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

// Buscar produtos (incluindo tamanho na query)
$query_produtos = "SELECT p.id, p.nome, p.valor_unidade, e.quantidade, e.tamanho 
                   FROM produtos p 
                   LEFT JOIN estoque e ON p.id = e.fk_produto_id 
                   WHERE e.quantidade > 0 
                   ORDER BY p.nome, e.tamanho";
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
                    <label for="fk_cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select name="fk_cliente_id" id="fk_cliente_id" class="form-select" required>
                        <option value="">Selecione um cliente</option>
                        <?php while ($cliente = mysqli_fetch_assoc($result_clientes)) : ?>
                            <option value="<?= $cliente['id'] ?>"><?= $cliente['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="fk_forma_pagto_id" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                    <select name="fk_forma_pagto_id" id="fk_forma_pagto_id" class="form-select" required>
                        <option value="">Selecione uma forma de pagamento</option>
                        <?php while ($forma = mysqli_fetch_assoc($result_formas)) : ?>
                            <option value="<?= $forma['id'] ?>"><?= $forma['descricao'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Produtos <span class="text-danger">*</span></label>
                    <div id="produtos-container">
                        <div class="row mb-2" data-index="0">
                            <div class="col-md-6">
                                <select name="produtos[0][id]" class="form-select produto-select" required>
                                    <option value="">Selecione um produto</option>
                                    <?php
                                    mysqli_data_seek($result_produtos, 0);
                                    while ($produto = mysqli_fetch_assoc($result_produtos)) : ?>
                                        <option value="<?= $produto['id'] . '-' . $produto['tamanho'] ?>"
                                            data-valor="<?= $produto['valor_unidade'] ?>"
                                            data-quantidade="<?= $produto['quantidade'] ?>">
                                            <?= $produto['nome'] ?> - Tamanho: <?= $produto['tamanho'] ?> - R$ <?= number_format($produto['valor_unidade'], 2, ',', '.') ?>
                                            (Estoque: <?= $produto['quantidade'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="produtos[0][quantidade]" class="form-control quantidade-input"
                                    placeholder="Quantidade" min="1" max="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <button type="button" class="btn btn-secondary btn-sm" id="adicionar-produto">Adicionar Produto</button>
                        <button type="button" class="btn btn-danger btn-sm" id="remover-produto" style="display: none;">Remover Produto</button>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Valor Total</label>
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

            // Função para atualizar o max do input de quantidade quando um produto é selecionado
            function atualizarQuantidadeMaxima(select) {
                const quantidadeInput = select.closest('.row').querySelector('.quantidade-input');
                const quantidadeDisponivel = select.options[select.selectedIndex].dataset.quantidade;
                quantidadeInput.max = quantidadeDisponivel;

                // Se a quantidade atual for maior que o novo máximo, ajusta para o máximo
                if (parseInt(quantidadeInput.value) > parseInt(quantidadeDisponivel)) {
                    quantidadeInput.value = quantidadeDisponivel;
                }

                // Atualiza o placeholder para mostrar a quantidade disponível
                quantidadeInput.placeholder = `Quantidade (máx: ${quantidadeDisponivel})`;
            }

            function adicionarProduto() {
                const novoProduto = document.createElement('div');
                novoProduto.className = 'row mb-2';
                novoProduto.setAttribute('data-index', produtoCount);
                novoProduto.innerHTML = `
                    <div class="col-md-6">
                        <select name="produtos[${produtoCount}][id]" class="form-select produto-select" required>
                            <option value="">Selecione um produto</option>
                            ${document.querySelector('.produto-select').innerHTML}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="number" name="produtos[${produtoCount}][quantidade]" class="form-control quantidade-input" 
                               placeholder="Quantidade" min="1" max="0" required>
                    </div>
                `;
                produtosContainer.appendChild(novoProduto);
                produtoCount++;

                const novoSelect = novoProduto.querySelector('.produto-select');
                novoSelect.addEventListener('change', function() {
                    atualizarQuantidadeMaxima(this);
                    atualizarValorTotal();
                });

                novoProduto.querySelector('.quantidade-input').addEventListener('input', atualizarValorTotal);

                if (produtosContainer.children.length > 1) {
                    document.getElementById('remover-produto').style.display = 'inline-block';
                }
            }

            // Adicionar listeners para o primeiro produto
            document.querySelector('.produto-select').addEventListener('change', function() {
                atualizarQuantidadeMaxima(this);
                atualizarValorTotal();
            });
            document.querySelector('.quantidade-input').addEventListener('input', atualizarValorTotal);

            // Adicionar listeners para os botões
            adicionarProdutoBtn.addEventListener('click', adicionarProduto);
            document.getElementById('remover-produto').addEventListener('click', function() {
                if (produtosContainer.children.length > 1) {
                    produtosContainer.lastElementChild.remove();
                    atualizarValorTotal();
                    produtoCount--;

                    if (produtosContainer.children.length === 1) {
                        document.getElementById('remover-produto').style.display = 'none';
                    }
                }
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