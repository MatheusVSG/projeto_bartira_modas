<?php
require_once '../../connection.php';
require_once '../../assets/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);


$tipoFiltro = isset($_GET['tipo_id']) ? $_GET['tipo_id'] : '';
$nomeFiltro = isset($_GET['nome_produto']) ? $_GET['nome_produto'] : '';

$query = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, t.nome as tipo_nome, e.tamanho, e.quantidade
          FROM produtos p
          LEFT JOIN tipos_produto t ON p.tipo_id = t.id
          LEFT JOIN estoque e ON p.id = e.fk_produto_id
          WHERE 1=1";

if ($tipoFiltro) {
    $query .= " AND t.id = '" . mysqli_real_escape_string($conn, $tipoFiltro) . "'";
}
if ($nomeFiltro) {
    $query .= " AND p.nome LIKE '%" . mysqli_real_escape_string($conn, $nomeFiltro) . "%'";
}

$result = mysqli_query($conn, $query);


ob_start();
?>

<h2 style="text-align:center;">Relatório de Estoque</h2>

<table border="1" width="100%" cellspacing="0" cellpadding="5">
    <thead style="background:#f1f1f1;">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Valor (R$)</th>
            <th>Tipo</th>
            <th>Tamanho</th>
            <th>Quantidade</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['produto_id'] ?></td>
                <td><?= $row['produto_nome'] ?></td>
                <td>R$ <?= isset($row['valor_unidade']) ? number_format($row['valor_unidade'], 2, ',', '.') : '-' ?></td>
                <td><?= $row['tipo_nome'] ?></td>
                <td><?= $row['tamanho'] ?? '-' ?></td>
                <td><?= $row['quantidade'] ?? '-' ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("relatorio_estoque.pdf", ["Attachment" => false]);
exit();
