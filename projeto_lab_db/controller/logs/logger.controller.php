<?php
function registrar_log($conn, $titulo, $descricao, $endereco = '', $link = '')
{
    $titulo = mysqli_real_escape_string($conn, $titulo);
    $descricao = mysqli_real_escape_string($conn, $descricao);
    $endereco = mysqli_real_escape_string($conn, $endereco);
    $link = mysqli_real_escape_string($conn, $link);

    $sql = "INSERT INTO logs (titulo, descricao, endereco, link) 
            VALUES ('$titulo', '$descricao', '$endereco', '$link')";

    mysqli_query($conn, $sql);
}
