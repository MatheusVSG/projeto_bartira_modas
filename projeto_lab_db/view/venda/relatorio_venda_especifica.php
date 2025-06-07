<?php

require_once '../../vendor/autoload.php';

require_once '../../connection.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verifica se o ID da venda foi fornecido ou se é válido
if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    header("Location: listar_vendas.php");
    exit();
}

$venda_id = $_GET['id'];

// Consulta SQL para obter os detalhes da venda
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
$stmt->bind_param("i", $venda_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: listar_vendas.php");
    exit();
}

$venda = $result->fetch_assoc();

// Consulta SQL para obter os itens da venda
$sql_itens = "SELECT p.nome, t.nome as tipo_nome, iv.qtd_vendida
              FROM item_venda iv
              INNER JOIN produtos p ON iv.fk_produto_id = p.id
              LEFT JOIN tipos_produto t ON p.tipo_id = t.id
              WHERE iv.fk_venda_id = ?";

$stmt_itens = $conn->prepare($sql_itens);
$stmt_itens->bind_param("i", $venda_id);
$stmt_itens->execute();
$itens_result = $stmt_itens->get_result();

// Configuração do DOMPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

// HTML do relatório
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
                    <th>Tipo</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>';

while ($item = $itens_result->fetch_assoc()) {
    $html .= '
                <tr>
                    <td>' . htmlspecialchars($item['nome']) . '</td>
                    <td>' . htmlspecialchars($item['tipo_nome'] ?? 'N/A') . '</td>
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

// Gera o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Define o nome do arquivo
$filename = 'relatorio_venda_' . $venda_id . '.pdf';

// Envia o PDF para o navegador
$dompdf->stream($filename, array('Attachment' => false));

// Fecha as conexões
$stmt->close();
$stmt_itens->close();
$conn->close();
