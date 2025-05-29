<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

if (!isset($_GET['id'])) {
    die('ID do produto não foi fornecido.');
}

$id = intval($_GET['id']);


$sqlFoto = "SELECT foto FROM produtos WHERE id = ?";
$stmtFoto = $conn->prepare($sqlFoto);
$stmtFoto->bind_param("i", $id);
$stmtFoto->execute();
$resultFoto = $stmtFoto->get_result();

if ($resultFoto->num_rows > 0) {
    $row = $resultFoto->fetch_assoc();
    $foto = $row['foto'];

    $caminhoFoto = "fotos/$foto";
    if (file_exists($caminhoFoto)) {
        unlink($caminhoFoto);
    }
}

$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    registrar_log(
        $conn,
        'Erro ao excluir produto',
        $stmt->error,
        $_SERVER['REQUEST_URI'],
        'controller/produto/excluir_produto.php',
        'produto'
    );
    echo "Erro ao excluir produto: " . $stmt->error;
    exit;
}

header("Location: ../../view/produto/cadastro-produto.php");
exit;
