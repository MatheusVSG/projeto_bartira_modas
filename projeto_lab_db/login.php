<?php
session_start();
include_once 'connection.php';

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login_vendedor'])) {
        $cpf = trim($_POST['cpf'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($cpf) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
            header("Location: index.php");
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
                    unset($_SESSION['error_message']);
                    exit;
                } else {
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                    header("Location: index.php");
                }
            } else {
                $_SESSION['error_message'] = "Credenciais inválidas!";
                header("Location: index.php");
            }
        }
    }


    if (isset($_POST['login_admin'])) {
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($usuario) || empty($senha)) {
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
            header("Location: index.php");
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
                    unset($_SESSION['error_message']);
                    exit;
                } else {
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                    header("Location: index.php");
                }
            } else {
                $_SESSION['error_message'] = "Credenciais inválidas!";
                header("Location: index.php");
            }
        }
    }
}
