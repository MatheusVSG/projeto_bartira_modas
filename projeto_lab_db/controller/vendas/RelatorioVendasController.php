<?php

require_once '../../connection.php';

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
}
