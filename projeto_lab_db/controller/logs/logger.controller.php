<?php
function registrar_log($conn, $titulo, $descricao, $endereco = '', $link = '', $tipo = 'geral')
{
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    $endereco = mysqli_real_escape_string($conn, $endereco);
    $link = mysqli_real_escape_string($conn, $link);

    $sql = "INSERT INTO logs (titulo, descricao, endereco, link) 
            VALUES ('$titulo', '$descricao', '$endereco', '$link')";

    mysqli_query($conn, $sql);

    // Salvar também em arquivo
    $data = date('Y-m-d');
    $hora = date('H:i:s');
    $dir = __DIR__ . "/$tipo";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $linha = "[$data $hora] $titulo | $descricao | $endereco | $link\n";
    file_put_contents("$dir/log_{$data}.txt", $linha, FILE_APPEND);
}
