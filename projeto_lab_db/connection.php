<?php
$host = "localhost";
$user = "root";
$senha = "";
$banco = "loja_de_roupas";

$conn = new mysqli($host, $user, $senha, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
