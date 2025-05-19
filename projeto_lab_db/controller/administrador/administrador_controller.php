<?php
include('../../connection.php');

if (isset($_POST['cadastrar'])) {
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);


    $sql = "SELECT * FROM administrador WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo "<script>alert('O nome de usuário já está em uso. Por favor, escolha outro.'); window.history.back();</script>";
    } else {

        $sql = "INSERT INTO administrador (usuario, senha) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $senha);
        $stmt->execute();
        header("Location: ../../view/administrador/listar_administrador.php");
    }
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "UPDATE administrador SET usuario=?, senha=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $usuario, $senha, $id);

    if ($stmt->execute()) {
        // Verificar se a atualização afetou alguma linha
        if ($stmt->affected_rows > 0) {
            echo "Dados atualizados com sucesso!";
            // Redireciona para a página de lista de administradores
            header("Location: ../../view/administrador/listar_administrador.php");
        } else {
            echo "Nenhuma alteração foi feita. Verifique se os dados já estão atualizados.";
        }
    } else {
        echo "Erro ao executar a consulta: " . $stmt->error;
    }
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $sql = "DELETE FROM administrador WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ../../view/administrador/listar_administrador.php");
}
