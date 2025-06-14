<?php
// Inclui o arquivo de conexão com o banco de dados (variável $conn)
require_once '../../connection.php';

// Inclui o autoload do Dompdf para carregar as classes necessárias
require_once '../../assets/dompdf/vendor/autoload.php';

// Importa as classes Dompdf e Options para uso
use Dompdf\Dompdf;
use Dompdf\Options;

// Cria uma instância de Options para configurar o Dompdf
$options = new Options();
// Habilita o parser HTML5 no Dompdf para melhor compatibilidade com HTML5
$options->set('isHtml5ParserEnabled', true);
// Define a fonte padrão usada no PDF como Arial
$options->set('defaultFont', 'Arial');

// Cria uma nova instância do Dompdf passando as opções configuradas
$dompdf = new Dompdf($options);

// Pega o filtro 'tipo_id' passado via GET, ou deixa vazio se não existir
$tipoFiltro = isset($_GET['tipo_id']) ? $_GET['tipo_id'] : '';
// Pega o filtro 'nome_produto' passado via GET, ou deixa vazio se não existir
$nomeFiltro = isset($_GET['nome_produto']) ? $_GET['nome_produto'] : '';

// Monta a consulta SQL básica para buscar os produtos e seu estoque
$query = "SELECT p.id as produto_id, p.nome as produto_nome, p.valor_unidade, p.foto, t.nome as tipo_nome, e.tamanho, e.quantidade
          FROM produtos p
          LEFT JOIN tipos_produto t ON p.tipo_id = t.id
          LEFT JOIN estoque e ON p.id = e.fk_produto_id
          WHERE 1=1"; // WHERE 1=1 facilita concatenar condições depois

// Se foi passado filtro de tipo, adiciona condição na query com proteção contra SQL Injection
if ($tipoFiltro) {
    $query .= " AND t.id = '" . mysqli_real_escape_string($conn, $tipoFiltro) . "'";
}
// Se foi passado filtro de nome do produto, adiciona condição LIKE na query
if ($nomeFiltro) {
    $query .= " AND p.nome LIKE '%" . mysqli_real_escape_string($conn, $nomeFiltro) . "%'";
}

// Executa a consulta no banco
$result = mysqli_query($conn, $query);

// Inicia o buffer de saída para capturar o HTML gerado
ob_start();
?>

<!-- Cabeçalho do relatório -->
<h2 style="text-align:center;">Relatório de Estoque</h2>

<!-- Tabela que exibirá os dados do estoque -->
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
        <!-- Loop para preencher cada linha da tabela com os dados do banco -->
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['produto_id'] ?></td>
                <td><?= $row['produto_nome'] ?></td>
                <!-- Formata o valor para moeda brasileira, ou mostra '-' se não existir -->
                <td>R$ <?= isset($row['valor_unidade']) ? number_format($row['valor_unidade'], 2, ',', '.') : '-' ?></td>
                <td><?= $row['tipo_nome'] ?></td>
                <!-- Se tamanho ou quantidade forem nulos, exibe '-' -->
                <td><?= $row['tamanho'] ?? '-' ?></td>
                <td><?= $row['quantidade'] ?? '-' ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
// Captura todo o conteúdo HTML gerado no buffer
$html = ob_get_clean();

// Carrega o HTML no Dompdf
$dompdf->loadHtml($html);

// Define o tamanho do papel e orientação (A4 retrato)
$dompdf->setPaper('A4', 'portrait');

// Gera o PDF a partir do HTML carregado
$dompdf->render();

// Envia o PDF para o navegador, sem forçar download (abre no navegador)
$dompdf->stream("relatorio_estoque.pdf", ["Attachment" => false]);

// Encerra o script para evitar saída extra
exit();
