<?php

// Importa a conexão com o banco de dados
require_once '../../connection.php';
// Importa a biblioteca do Dompdf para gerar PDF
require '../../assets/dompdf/vendor/autoload.php';

// Usa o namespace da classe Dompdf para facilitar a criação do objeto
use Dompdf\Dompdf;

// Define uma classe para controlar a geração de relatórios de vendas
class RelatorioVendasController
{
    // Método que busca os dados do relatório de vendas agrupados por ano e mês
    public function gerarRelatorio()
    {
        // Usa a variável global $conn da conexão com o banco
        global $conn;

        // Consulta SQL que seleciona ano, mês e soma do valor vendido, agrupando por ano e mês
        $sql = "
        SELECT YEAR(v.data_criacao) AS ano, MONTH(v.data_criacao) AS mes, SUM(v.valor) AS total_vendido
        FROM vendas v
        GROUP BY YEAR(v.data_criacao), MONTH(v.data_criacao)
        ORDER BY ano, mes;
    ";

        // Executa a consulta
        $result = mysqli_query($conn, $sql);

        // Se a consulta foi executada com sucesso, processa os dados
        if ($result) {
            $dados = [];
            // Busca linha por linha e adiciona ao array $dados
            while ($row = mysqli_fetch_assoc($result)) {
                $dados[] = $row;
            }
            // Retorna os dados coletados
            return $dados;
        } else {
            // Em caso de erro na consulta, retorna false
            return false;
        }
    }

    // Método que gera o relatório em PDF usando os dados obtidos
    public function gerarRelatorioPDF()
    {
        // Chama o método para obter os dados
        $dados = $this->gerarRelatorio();

        // Se não conseguiu obter dados, exibe erro e encerra
        if (!$dados) {
            die("Erro ao buscar dados do relatório.");
        }

        // Inicia o buffer de saída para capturar o HTML do relatório
        ob_start();
        // Gera o título do relatório centralizado
        echo '<h2 style="text-align:center;">Relatório de Vendas por Mês</h2>';
        // Começa a tabela com borda e largura total
        echo '<table border="1" width="100%" cellspacing="0" cellpadding="5">';
        // Cabeçalho da tabela com fundo cinza claro
        echo '<thead style="background:#eee;"><tr><th>Ano</th><th>Mês</th><th>Total Vendido (R$)</th></tr></thead><tbody>';
        // Loop para preencher cada linha da tabela com os dados do relatório
        foreach ($dados as $d) {
            echo '<tr>';
            // Ano
            echo '<td>' . $d['ano'] . '</td>';
            // Mês com zero à esquerda para ficar no formato 01, 02, ...
            echo '<td>' . str_pad($d['mes'], 2, '0', STR_PAD_LEFT) . '</td>';
            // Valor total vendido formatado como moeda brasileira
            echo '<td>R$ ' . number_format($d['total_vendido'], 2, ',', '.') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        // Finaliza o buffer e captura o HTML gerado
        $html = ob_get_clean();

        // Cria o objeto Dompdf para gerar o PDF
        $dompdf = new Dompdf();
        // Carrega o HTML que será convertido em PDF
        $dompdf->loadHtml($html);
        // Define o formato do papel como A4 e orientação retrato
        $dompdf->setPaper('A4', 'portrait');
        // Processa o HTML e gera o PDF
        $dompdf->render();
        // Exibe o PDF no navegador, sem forçar download (Attachment = false)
        $dompdf->stream("relatorio_vendas_mensal.pdf", ["Attachment" => false]);
        // Encerra o script
        exit();
    }
}
