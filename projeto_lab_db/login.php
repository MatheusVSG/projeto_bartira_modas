<?php
// Inicia ou retoma a sessão atual
session_start();

// Verifica se o método da requisição HTTP é POST, ou seja, se um formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inclui o arquivo de conexão com o banco de dados (assumindo que define $conn)
    include_once 'connection.php';

    // Verifica se houve erro na conexão com o banco de dados
    if ($conn->connect_error) {
        // Para a execução e exibe mensagem de erro se a conexão falhar
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Verifica se o formulário enviado é o de login de vendedor
    if (isset($_POST['login_vendedor'])) {
        // Obtém o CPF enviado no formulário, removendo espaços em branco nas extremidades
        $cpf = trim($_POST['cpf'] ?? '');
        // Obtém a senha enviada no formulário, removendo espaços em branco nas extremidades
        $senha = trim($_POST['senha'] ?? '');

        // Verifica se CPF ou senha estão vazios
        if (empty($cpf) || empty($senha)) {
            // Armazena uma mensagem de erro na sessão para mostrar na página de login
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
            // Redireciona de volta para a página de login
            header("Location: index.php");
        } else {
            // Prepara uma consulta SQL para buscar o vendedor pelo CPF
            $sql = "SELECT * FROM vendedores WHERE cpf = ?";
            $stmt = $conn->prepare($sql);
            // Liga o parâmetro CPF na consulta preparada para evitar SQL Injection
            $stmt->bind_param("s", $cpf);
            // Executa a consulta
            $stmt->execute();
            // Obtém o resultado da consulta
            $result = $stmt->get_result();

            // Verifica se encontrou algum vendedor com o CPF informado
            if ($result->num_rows > 0) {
                // Busca os dados do vendedor em formato de array associativo
                $vendedor = $result->fetch_assoc();
                $conn->close();
                // Verifica se a senha informada corresponde ao hash armazenado no banco
                if (password_verify($senha, $vendedor['senha'])) {
                    // Define variáveis de sessão para indicar que o vendedor está logado
                    $_SESSION['usuario_id'] = $vendedor['id'];
                    $_SESSION['tipo_usuario'] = 'vendedor';
                    // Regenera o ID da sessão para maior segurança
                    session_regenerate_id();
                    // Redireciona para a home do vendedor
                    header("Location: view/vendedor/home_vendedor.php");
                    // Remove qualquer mensagem de erro antiga da sessão
                    unset($_SESSION['error_message']);
                    // Termina a execução do script após o redirecionamento
                    exit;
                } else {
                    // Se a senha não bate, salva mensagem de erro na sessão
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                    // Redireciona para o login
                    header("Location: index.php");
                }
            } else {
                $conn->close();
                // Se não encontrou vendedor com o CPF, mostra erro igual
                $_SESSION['error_message'] = "Credenciais inválidas!";
                header("Location: index.php");
            }
        }
    }

    // Verifica se o formulário enviado é o de login de administrador
    if (isset($_POST['login_admin'])) {
        // Obtém o usuário e a senha enviados no formulário, removendo espaços
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        // Verifica se usuário ou senha estão vazios
        if (empty($usuario) || empty($senha)) {
            // Mensagem de erro para campos vazios
            $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
            header("Location: index.php");
        } else {
            // Prepara consulta para buscar o administrador pelo usuário
            $sql = "SELECT * FROM administrador WHERE usuario = ?";
            $stmt = $conn->prepare($sql);
            // Liga o parâmetro usuário na consulta
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verifica se encontrou o administrador
            if ($result->num_rows > 0) {
                // Busca os dados do administrador
                $admin = $result->fetch_assoc();
                $conn->close();
                // Verifica se a senha informada confere com o hash armazenado
                if (password_verify($senha, $admin['senha'])) {
                    // Define as variáveis de sessão para o admin logado
                    $_SESSION['usuario_id'] = $admin['id'];
                    $_SESSION['tipo_usuario'] = 'admin';
                    session_regenerate_id();
                    // Redireciona para a home do administrador
                    header("Location: view/administrador/home_adm.php");
                    unset($_SESSION['error_message']);
                    exit;
                } else {
                    $_SESSION['error_message'] = "Credenciais inválidas!";
                    header("Location: index.php");
                }
            } else {
                $conn->close();
                $_SESSION['error_message'] = "Credenciais inválidas!";
                header("Location: index.php");
            }
        }
    }
}
