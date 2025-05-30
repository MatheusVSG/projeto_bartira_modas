<?php
include_once '../../connection.php';
include('../logs/logger.controller.php');

$id = $_POST['id'];
$nome = $_POST['nome'];
$valor = $_POST['valor_unidade'];
$tipo_id = $_POST['tipo_id'];

try {
    if (!empty($_FILES['foto']['name'])) {
        $foto = uniqid() . "_" . $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];

        move_uploaded_file($foto_tmp, "../../view/produto/fotos/$foto");

        $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, foto = ?, tipo_id = ?, modificado_por = 'admin' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsii", $nome, $valor, $foto, $tipo_id, $id);
    } else {
        $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, tipo_id = ?, modificado_por = 'admin' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdii", $nome, $valor, $tipo_id, $id);
    }

    if ($stmt->execute()) {

        $sql_delete = "DELETE FROM estoque WHERE fk_produto_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();


        if (isset($_POST['tamanhos']) && is_array($_POST['tamanhos'])) {
            foreach ($_POST['tamanhos'] as $index => $tamanho) {
                $tamanho = mysqli_real_escape_string($conn, $tamanho);
                $quantidade = $_POST['quantidades'][$index];

                $sql_estoque = "INSERT INTO estoque (tamanho, fk_produto_id, quantidade) 
                               VALUES (?, ?, ?)";
                $stmt_estoque = $conn->prepare($sql_estoque);
                $stmt_estoque->bind_param("sii", $tamanho, $id, $quantidade);
                $stmt_estoque->execute();
            }
        }

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
        '../controller/produto_atualizar_produto.php',
        'produto'
    );

    echo "Erro ao atualizar produto: " . $e->getMessage();
}
