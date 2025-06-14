<?php
// Inicia a sessão para acessar variáveis como tipo de usuário
session_start();

// Carrega automaticamente todas as dependências (inclui o DomPDF)
require_once '../../vendor/autoload.php';

// Usa as classes da biblioteca DomPDF
use Dompdf\Dompdf;
use Dompdf\Options;

// Verifica se o usuário está logado como administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado! Administrador não autenticado';
    header("Location: ../../"); // Redireciona para a página inicial
    exit();
}

// Verifica se o ID da venda foi passado pela URL e se é válido
if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'ID de venda inválido';
    header("Location: listar_vendas.php");
    exit();
}

$venda_id = $_GET['id']; // Armazena o ID da venda

try {
    require_once '../../connection.php'; // Conexão com o banco de dados

    // Consulta principal para obter detalhes da venda, cliente, vendedor e forma de pagamento
    $sql = "SELECT v.id AS venda_id, v.valor AS valor_total, v.data_criacao AS data_venda,
               c.nome AS cliente_nome, c.telefone AS cliente_telefone, c.email AS cliente_email,
               ve.nome AS vendedor_nome, ve.telefone AS vendedor_telefone, ve.email AS vendedor_email,
               fp.descricao AS forma_pagamento
        FROM vendas v
        INNER JOIN clientes c ON v.fk_cliente_id = c.id
        INNER JOIN vendedores ve ON v.fk_vendedor_id = ve.id
        INNER JOIN forma_pagto fp ON v.fk_forma_pagto_id = fp.id
        WHERE v.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $venda_id); // Substitui o "?" pelo ID da venda
    $stmt->execute();
    $result = $stmt->get_result();

    // Se não encontrar nenhuma venda com o ID, redireciona
    if ($result->num_rows === 0) {
        header("Location: listar_vendas.php");
        exit();
    }

    $venda = $result->fetch_assoc(); // Recupera os dados da venda

    // Consulta para buscar os produtos e quantidades vendidos nessa venda
    $sql_itens = "SELECT p.nome, iv.qtd_vendida
              FROM item_venda iv
              INNER JOIN produtos p ON iv.fk_produto_id = p.id
              WHERE iv.fk_venda_id = ?
              ORDER BY p.nome ASC";

    $stmt_itens = $conn->prepare($sql_itens);
    $stmt_itens->bind_param("i", $venda_id); // Substitui o "?" pelo ID da venda
    $stmt_itens->execute();
    $itens_result = $stmt_itens->get_result();

    // Se não houver itens vinculados à venda, redireciona
    if ($itens_result->num_rows === 0) {
        header("Location: listar_vendas.php");
        exit();
    }

    // Configuração das opções do DomPDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true); // Habilita o parser HTML5
    $options->set('isPhpEnabled', true); // Permite código PHP no HTML (caso use)

    // Cria uma instância do Dompdf
    $dompdf = new Dompdf($options);

    // Início do HTML que será convertido em PDF
    $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Venda #' . $venda['venda_id'] . '</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .info-block { margin-bottom: 20px; }
        .info-block h3 { margin-bottom: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { text-align: right; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bartira Modas</h1>
        <h2>Relatório de Venda #' . $venda['venda_id'] . '</h2>
        <p>Data: ' . date("d/m/Y H:i:s", strtotime($venda['data_venda'])) . '</p>
    </div>

    <div class="info-block">
        <h3>Informações do Cliente</h3>
        <p><strong>Nome:</strong> ' . htmlspecialchars($venda['cliente_nome']) . '</p>
        <p><strong>Telefone:</strong> ' . htmlspecialchars($venda['cliente_telefone']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($venda['cliente_email']) . '</p>
    </div>

    <div class="info-block">
        <h3>Informações do Vendedor</h3>
        <p><strong>Nome:</strong> ' . htmlspecialchars($venda['vendedor_nome']) . '</p>
        <p><strong>Telefone:</strong> ' . htmlspecialchars($venda['vendedor_telefone']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($venda['vendedor_email']) . '</p>
    </div>

    <div class="info-block">
        <h3>Itens da Venda</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>';

    // Laço para inserir os itens da venda no HTML
    while ($item = $itens_result->fetch_assoc()) {
        $html .= '
                <tr>
                    <td>' . htmlspecialchars($item['nome']) . '</td>
                    <td>' . htmlspecialchars($item['qtd_vendida']) . '</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>
    </div>

    <div class="info-block">
        <h3>Informações de Pagamento</h3>
        <p><strong>Forma de Pagamento:</strong> ' . htmlspecialchars($venda['forma_pagamento']) . '</p>
        <p class="total">Valor Total: R$ ' . number_format($venda['valor_total'], 2, ',', '.') . '</p>
    </div>
</body>
</html>';

    // Carrega o HTML no DomPDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait'); // Define o tamanho e orientação da página
    $dompdf->render(); // Renderiza o conteúdo

    // Define o nome do arquivo PDF
    $filename = 'relatorio_venda_' . $venda_id . '.pdf';

    // Exibe o PDF no navegador (sem forçar download)
    $dompdf->stream($filename, array('Attachment' => false));
} catch (Exception $e) {
    // Aqui poderia ser exibida uma mensagem de erro, mas está vazio
} finally {
    // Fecha os recursos do banco de dados
    $stmt->close();
    $stmt_itens->close();
    $conn->close();
}
