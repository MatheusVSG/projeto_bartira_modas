<?php
session_start();
include_once 'connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (isset($_POST['login_vendedor'])) {
        $cpf = $_POST['cpf'];
        $senha = $_POST['senha'];

        if (empty($cpf) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
        } else {
            $sql = "SELECT * FROM vendedores WHERE cpf = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $cpf);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $vendedor = $result->fetch_assoc();
                if (password_verify($senha, $vendedor['senha'])) {
                    $_SESSION['usuario_id'] = $vendedor['id'];
                    $_SESSION['tipo_usuario'] = 'vendedor';
                    session_regenerate_id();
                    header("Location: view/vendedor/home_vendedor.php");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                }
            } else {
                $_SESSION['error_message'] = "Credenciais inválidas!";
            }
        }
    }


    if (isset($_POST['login_admin'])) {
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];

        if (empty($usuario) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
        } else {
            $sql = "SELECT * FROM administrador WHERE usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                if (password_verify($senha, $admin['senha'])) {
                    $_SESSION['usuario_id'] = $admin['id'];
                    $_SESSION['tipo_usuario'] = 'admin';
                    session_regenerate_id();
                    header("Location: view/administrador/home_adm.php");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                }
            } else {
                $_SESSION['error_message'] = "Credenciais inválidas!";
            }
        }
    }
}


if (isset($_SESSION['error_message'])) {
    echo "<div style='color: red; text-align: center;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
