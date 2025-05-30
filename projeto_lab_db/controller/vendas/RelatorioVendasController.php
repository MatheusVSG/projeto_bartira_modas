<?php

require_once '../../connection.php';
require '../../assets/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;

class RelatorioVendasController
{
    public function gerarRelatorio()
    {
        global $conn;

        $sql = "
        SELECT YEAR(v.data_venda) AS ano, MONTH(v.data_venda) AS mes, SUM(v.valor_total) AS total_vendido
        FROM vendas v
        GROUP BY YEAR(v.data_venda), MONTH(v.data_venda)
        ORDER BY ano, mes;
        ";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $dados = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $dados[] = $row;
            }
            return $dados;
        } else {
            return false;
        }
    }

    public function gerarRelatorioPDF()
    {
        $dados = $this->gerarRelatorio();

        if (!$dados) {
            die("Erro ao buscar dados do relatório.");
        }

        ob_start();
        echo '<h2 style="text-align:center;">Relatório de Vendas por Mês</h2>';
        echo '<table border="1" width="100%" cellspacing="0" cellpadding="5">';
        echo '<thead style="background:#eee;"><tr><th>Ano</th><th>Mês</th><th>Total Vendido (R$)</th></tr></thead><tbody>';
        foreach ($dados as $d) {
            echo '<tr>';
            echo '<td>' . $d['ano'] . '</td>';
            echo '<td>' . str_pad($d['mes'], 2, '0', STR_PAD_LEFT) . '</td>';
            echo '<td>R$ ' . number_format($d['total_vendido'], 2, ',', '.') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("relatorio_vendas_mensal.pdf", ["Attachment" => false]);
        exit();
    }
}
