<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (
    !isset($_SESSION['usuario_id']) ||
    !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])
) {
    header("Location: ../../login.php");
    exit();
}

$vendedor_id = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Bartira Modas | Realizar Venda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">

    <div class="container py-4">
        <div class="bg-light text-dark p-4 rounded shadow">
            <h2 class="text-center mb-4">Nova Venda</h2>

            <form method="POST" action="../../controller/vendas/venda_controller.php">

                <div class="mb-3">
                    <label for="cliente_nome">Cliente:</label>
                    <input type="text" id="cliente_nome" class="form-control" placeholder="Digite o nome do cliente..." required>
                    <input type="hidden" name="fk_cliente_id" id="fk_cliente_id">
                </div>


                <input type="hidden" name="fk_vendedor_id" value="<?= $vendedor_id ?>">

                <div class="mb-3">
                    <label for="fk_forma_pagto_id">Forma de Pagamento:</label>
                    <select name="fk_forma_pagto_id" class="form-control" required>
                        <option value="">Selecione</option>
                        <?php
                        $formas = mysqli_query($conn, "SELECT id, descricao FROM forma_pagto");
                        while ($f = mysqli_fetch_assoc($formas)) {
                            echo "<option value='{$f['id']}'>{$f['descricao']}</option>";
                        }
                        ?>
                    </select>
                </div>


                <div class="mb-3">
                    <label>Buscar Produto:</label>
                    <input type="text" id="busca-produto" class="form-control" placeholder="Digite o nome do produto...">
                </div>


                <div class="mb-3">
                    <table class="table table-sm table-bordered" id="tabela-itens">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


                <div class="mb-3">
                    <label>Valor Total:</label>
                    <input type="text" id="valor-total" name="valor" class="form-control" readonly>
                </div>


                <div id="produtos-selecionados"></div>

                <div class="d-flex justify-content-between">
                    <?php
                    if ($_SESSION['tipo_usuario'] == 'admin') {
                        echo '<a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>';
                    } elseif ($_SESSION['tipo_usuario'] == 'vendedor') {
                        echo '<a href="../vendedor/home_vendedor.php" class="btn btn-secondary btn-sm">Voltar</a>';
                    }
                    ?>
                    <button type="submit" name="cadastrar_venda" class="btn btn-success">Finalizar Venda</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        let total = 0;
        let contador = 0;

        $(function() {

            $("#busca-produto").autocomplete({
                source: function(request, response) {
                    $.getJSON("../../controller/produto/produto_buscar.php", {
                        termo: request.term
                    }, function(data) {
                        response(data.map(item => ({
                            label: item.nome + " - R$" + parseFloat(item.valor_unidade).toFixed(2),
                            value: item.nome,
                            id: item.id,
                            preco: parseFloat(item.valor_unidade)
                        })));
                    });
                },
                select: function(event, ui) {
                    total += ui.item.preco;
                    $("#valor-total").val(total.toFixed(2));

                    $("#tabela-itens tbody").append(`
                    <tr id="item-${contador}">
                        <td>${ui.item.label}</td>
                        <td>R$ ${ui.item.preco.toFixed(2)}</td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="removerItem(${contador}, ${ui.item.preco})">Remover</button></td>
                    </tr>
                `);

                    $("#produtos-selecionados").append(`
                    <input type="hidden" name="produtos[${contador}][id]" value="${ui.item.id}">
                    <input type="hidden" name="produtos[${contador}][preco]" value="${ui.item.preco}">
                `);

                    contador++;
                    $(this).val("");
                    return false;
                }
            });


            $("#cliente_nome").autocomplete({
                source: function(request, response) {
                    $.getJSON("../../controller/cliente/cliente_buscar.php", {
                        termo: request.term
                    }, function(data) {
                        response(data.map(item => ({
                            label: item.nome + " - CPF: " + item.cpf,
                            value: item.nome,
                            id: item.id
                        })));
                    });
                },
                select: function(event, ui) {
                    $("#fk_cliente_id").val(ui.item.id);
                    $("#cliente_nome").val(ui.item.value);
                    return false;
                }
            });
        });

        function removerItem(index, preco) {
            $("#item-" + index).remove();
            $(`#produtos-selecionados input[name="produtos[${index}][id]"]`).remove();
            $(`#produtos-selecionados input[name="produtos[${index}][preco]"]`).remove();

            total -= preco;
            $("#valor-total").val(total.toFixed(2));
        }
    </script>
</body>

</html>