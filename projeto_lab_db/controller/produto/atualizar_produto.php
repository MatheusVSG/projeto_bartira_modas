<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$id = $_POST['id'];
$nome = $_POST['nome'];
$valor = $_POST['valor_unidade'];

try {
    if (!empty($_FILES['foto']['name'])) {
        $foto = uniqid() . "_" . $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];

        move_uploaded_file($foto_tmp, "../../view/produto/fotos/$foto");

        $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, foto = ?, modificado_por = 'admin' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsi", $nome, $valor, $foto, $id);
    } else {
        $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, modificado_por = 'admin' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $nome, $valor, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../../view/produto/cadastro-produto.php");
        exit;
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao atualizar produto',
        $e->getMessage(),
        $_SERVER['REQUEST_URI'],
        '../controller/produto_atualizar_produto.php'
    );
    echo "Erro ao atualizar produto: " . $e->getMessage();
}
