<?php
session_start();
include '../../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $tipo_id = $_POST['tipo_id'];
    $valor_unidade = $_POST['valor_unidade'];

    // Faz o upload da foto (se houver)
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nome_original = $_FILES['foto']['name'];
        $ext = pathinfo($nome_original, PATHINFO_EXTENSION);
        $foto = uniqid() . '.' . $ext;
        $destino = "../../view/produto/fotos/" . $foto;
        $foto_tmp = $_FILES['foto']['tmp_name'];
        
        // Verifica se o diretório existe
        if (!file_exists("../../view/produto/fotos/")) {
            mkdir("../../view/produto/fotos/", 0777, true);
        }
        
        // Tenta fazer o upload
        if (!move_uploaded_file($foto_tmp, $destino)) {
            $_SESSION['error_message'] = 'Erro ao fazer upload da foto!';
            header("Location: ../../view/produto/cadastro-produto.php");
            exit();
        }
    }

    // Prepara e executa a inserção do produto
    $sql = "INSERT INTO produtos (nome, tipo_id, valor_unidade, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sids", $nome, $tipo_id, $valor_unidade, $foto);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Produto cadastrado com sucesso!';
        header("Location: ../../view/produto/cadastro-produto.php");
        exit();
    } else {
        $_SESSION['error_message'] = 'Erro ao cadastrar produto: ' . $conn->error;
        header("Location: ../../view/produto/cadastro-produto.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Requisição inválida.';
    header("Location: ../../view/produto/cadastro-produto.php");
    exit();
}
?> 