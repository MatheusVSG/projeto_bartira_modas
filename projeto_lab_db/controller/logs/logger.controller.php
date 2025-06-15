<?php
// Função para registrar logs no banco de dados e em arquivos de texto
function registrar_log($conn, $titulo, $descricao, $endereco = '', $link = '', $tipo = 'geral')
{
    // Protege as variáveis contra injeção SQL, escapando caracteres especiais
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    $endereco = mysqli_real_escape_string($conn, $endereco);

    // Monta a query para inserir o log na tabela 'logs' no banco de dados
    $sql = "INSERT INTO logs (titulo, descricao, endereco) VALUES ('$titulo', '$descricao', '$endereco')";
    // Executa a query no banco
    mysqli_query($conn, $sql);

    // Obtém a data atual no formato ano-mês-dia
    $data = date('Y-m-d');
    // Obtém a hora atual no formato horas:minutos:segundos
    $hora = date('H:i:s');

    // Define o diretório para salvar o arquivo de log, com base no tipo (ex: 'geral')
    $dir = __DIR__ . "/$tipo";

    // Verifica se o diretório existe; se não, cria com permissão 0777 e modo recursivo (cria pastas necessárias)
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Prepara a linha que será escrita no arquivo de log (data, hora, título, descrição, endereço e link)
    $linha = "[$data $hora] $titulo | $descricao | $endereco | $link\n";

    // Escreve a linha no arquivo de texto dentro do diretório, com nome contendo a data atual
    // O FILE_APPEND garante que a linha será adicionada ao final do arquivo, sem apagar conteúdo anterior
    file_put_contents("$dir/log_{$data}.txt", $linha, FILE_APPEND);
}
