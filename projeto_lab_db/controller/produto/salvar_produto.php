<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$nome = $_POST['nome'];
$valor = $_POST['valor_unidade'];
$tipo_id = $_POST['tipo_id'];
$foto = $_FILES['foto']['name'];
$foto_tmp = $_FILES['foto']['tmp_name'];

$ext = pathinfo($foto, PATHINFO_EXTENSION);
$foto = uniqid() . '.' . $ext;
$destino = "../../view/produto/fotos/$foto";
move_uploaded_file($foto_tmp, $destino);

$sql = "INSERT INTO produtos (nome, valor_unidade, foto, modificado_por, tipo_id) 
        VALUES ('$nome', '$valor', '$foto', 'admin', '$tipo_id')";

if ($conn->query($sql)) {
        header("Location: ../../view/produto/cadastro-produto.php");
        exit;
} else {
        registrar_log(
                $conn,
                'Erro ao cadastrar produto',
                $conn->error,
                $_SERVER['REQUEST_URI'],
                'controller/produto/salvar_produto.php',
                'produto'
        );
        echo "Erro ao cadastrar produto: " . $conn->error;
}
