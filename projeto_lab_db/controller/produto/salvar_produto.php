<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado! Administrador não autenticado';
    header('Location: ../../');
    exit();
}

if (!isset($_POST['produto_id'])) {
    $_SESSION['error_message'] = 'O produto fornecido é inválido!';
    header('Location: ../../view/produto/cadastro-produto.php');
    exit();
}

$produto_id = $_POST['produto_id'];

try {
    include_once '../../connection.php';
    include('../logs/logger.controller.php');

    $conn->begin_transaction();
    // Adiciona um novo produto
    if ($produto_id == 'novo_produto') {
        $nome = $_POST['nome'];
        $valor = $_POST['valor_unidade'];
        $tipo_id = $_POST['tipo_id'];

        if (empty(trim($nome))) {
            $_SESSION['error_message'] = 'O não pode ficar em branco!';
            header('Location: ../../view/produto/cadastro-produto.php');
            exit();
        }

        if (!is_numeric($valor)) {
            $_SESSION['error_message'] = 'O valor deve ser numérico!';
            header('Location: ../../view/produto/cadastro-produto.php');
            exit();
        }

        if (!is_numeric($tipo_id) || $tipo_id <= 0) {
            $_SESSION['error_message'] = 'Tipo de produto inválido!';
            header('Location: ../../view/produto/cadastro-produto.php');
            exit();
        }

        // Verifica se o produto já existe
        $sql = "SELECT p.nome FROM produtos AS p
        WHERE p.nome = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $nome);

        if ($stmt->execute()) {
            $produtoExiste = $stmt->get_result();

            if ($produtoExiste->num_rows > 0) {
                $stmt->close();
                $_SESSION['warning_message'] = 'Produto já cadastrado! <a href="../estoque/listar_estoque.php"><strong class="text-dark">Ver na lista</strong>.</a>';
                header('Location: ../../view/produto/cadastro-produto.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Erro ao verificar se o produto já está cadastrado!';
            throw new Exception('Erro ao verificar se o produto já está cadastrado!');
        }

        // Faz o upload da foto (se houver)
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
                throw new Exception('Erro ao fazer upload da foto!');
            }

            // Verifica se o arquivo foi realmente criado
            if (!file_exists($destino)) {
                $_SESSION['error_message'] = 'Erro ao salvar a foto!';
                throw new Exception('Erro ao salvar a foto!');
            }
        } else {
            $foto = '';
        }

        $sql = "INSERT INTO produtos (nome, valor_unidade, foto, modificado_por, tipo_id)
        VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdsii', $nome, $valor, $foto, $_SESSION['usuario_id'], $tipo_id);

        if ($stmt->execute()) {
            $produto_id = $stmt->insert_id;
            $stmt->close();

            $tamanho = $_POST['tamanho'];
            $quantidade = $_POST['quantidade'];

            if (empty($tamanho)) {
                $tamanho = '';
            }

            // Adiciona o produto ao estoque
            $sql_estoque = "INSERT INTO estoque (tamanho, fk_produto_id, quantidade) VALUES (?, ?, ?)";
            $stmt_estoque = $conn->prepare($sql_estoque);
            $stmt_estoque->bind_param("sii", $tamanho, $produto_id, $quantidade);

            if ($stmt_estoque->execute()) {
                $stmt_estoque->close();

                $conn->commit();
                $_SESSION['success_message'] = 'Produto cadastrado com sucesso!';
                header('Location: ../../view/produto/cadastro-produto.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Erro ao adicionar o item ' . htmlspecialchars($tamanho) . ' ao estoque.';
                throw new Exception('Erro ao adicionar o item ' . htmlspecialchars($tamanho) . ' ao estoque.');
            }
        } else {
            $_SESSION['error_message'] = 'Erro ao cadastrar produto!';
            throw new Exception('Erro ao cadastrar produto!');
        }
    }
    // Adiciona somente o tamanho e a quantidade caso o produto já exista
    else {
        if (!is_numeric($produto_id) || $produto_id <= 0) {
            $conn->rollback();
            $_SESSION['error_message'] = 'ID do produto inválido!';
            header('Location: ../../view/produto/cadastro-produto.php');
            exit();
        }

        $tamanho = $_POST['tamanho'];
        $quantidade = $_POST['quantidade'];

        if (empty(trim($tamanho))) {
            $tamanho = '';
        }

        // Faz o upload da foto (se houver)
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
                throw new Exception('Erro ao fazer upload da foto!');
            }

            // Atualiza a foto do produto
            $sql_foto = "UPDATE produtos SET foto = ? WHERE id = ?";
            $stmt_foto = $conn->prepare($sql_foto);
            $stmt_foto->bind_param("si", $foto, $produto_id);
            
            if (!$stmt_foto->execute()) {
                $_SESSION['error_message'] = 'Erro ao atualizar a foto do produto!';
                throw new Exception('Erro ao atualizar a foto do produto!');
            }
            $stmt_foto->close();
        }

        $sql_check = "SELECT id FROM estoque WHERE fk_produto_id = ? AND tamanho = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $produto_id, $tamanho);

        if ($stmt_check->execute()) {
            $resultado = $stmt_check->get_result();

            if ($resultado->num_rows === 0) {
                $sql_estoque = "INSERT INTO estoque (tamanho, fk_produto_id, quantidade) VALUES (?, ?, ?)";
                $stmt_estoque = $conn->prepare($sql_estoque);
                $stmt_estoque->bind_param("sii", $tamanho, $produto_id, $quantidade);

                if ($stmt_estoque->execute()) {
                    $stmt_estoque->close();
                    $conn->commit();
                    $_SESSION['success_message'] = 'Tamanho(s) adicionados ao estoque!';
                    header('Location: ../../view/produto/cadastro-produto.php');
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Erro ao adicionar o tamanho ' . htmlspecialchars($tamanho) . ' ao estoque.';
                    throw new Exception('Erro ao adicionar o tamanho ' . htmlspecialchars($tamanho) . ' ao estoque.');
                }
            } else {
                $_SESSION['warning_message'] = 'Tamanho já cadastrado para o produto informado! <a href="../estoque/listar_estoque.php"><strong class="text-dark">Ver na lista</strong>.</a>';
                header('Location: ../../view/produto/cadastro-produto.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Erro ao executar verificação para o tamanho ' . htmlspecialchars($tamanho);
            throw new Exception('Erro ao executar verificação para o tamanho ' . htmlspecialchars($tamanho));
        }
    }
} catch (Exception $e) {
    $conn->rollback();

    registrar_log(
        $conn,
        'Erro ao cadastrar produto',
        $conn->error,
        $_SERVER['REQUEST_URI'],
        'controller/produto/salvar_produto.php',
        'produto'
    );

    header('Location: ../../view/produto/cadastro-produto.php');
    exit();
}
