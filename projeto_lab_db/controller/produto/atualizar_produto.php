<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['error_message'] = 'Acesso inválido. Requisição não permitida!';
    header("Location: ../../view/estoque/listar_estoque.php");
    exit();
}

if (
    !isset($_SESSION['tipo_usuario'])
    || $_SESSION['tipo_usuario'] != 'admin'
    || !isset($_SESSION['usuario_id'])
) {
    $_SESSION['error_message'] = 'Acesso inválido. Administrador não autenticado!';
    header("Location: ../../view/estoque/listar_estoque.php");
    exit();
}

$adminId = $_SESSION['usuario_id'];
$atualizacaoCompleta = $_POST['atualizacao_completa'] ?? null;
$id = $_POST['id'];
$nome = $_POST['nome'] ?? '';
$tamanho = $_POST['tamanho'] ?? null;
$quantidade = $_POST['quantidade'];
$valor = str_replace(',', '.', $_POST['valor_unidade']);
$tipo_id = $_POST['tipo_id'];

if (!is_numeric($id) || $id <= 0) {
    $_SESSION['error_message'] = 'ID do produto inválido!';
    header("Location: ../../view/estoque/listar_estoque.php");
    exit();
}

if (!is_numeric($valor)) {
    $_SESSION['error_message'] = 'Valor do produto inválido!';
    header("Location: ../../view/estoque/listar_estoque.php");
    exit();
}

try {
    include_once '../../connection.php';
    include('../logs/logger.controller.php');

    $conn->begin_transaction();
    // Se a atualização for feita via página de detalhes do produto
    if (isset($atualizacaoCompleta)) {
        if (empty(trim($nome))) {
            $_SESSION['error_message'] = 'O nome não pode ficar em branco!';
            header("Location: ../../view/produto/editar_produto.php?id=$id&tamanho=$tamanho");
            exit();
        }

        $sql = "SELECT p.id FROM produtos AS p
        WHERE p.nome = ? AND p.id <> ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $nome, $id);

        if ($stmt->execute()) {
            $produtoExiste = $stmt->get_result();

            if ($produtoExiste->num_rows > 0) {
                $stmt->close();
                $_SESSION['warning_message'] = 'Nome indisponível! <a href="../estoque/listar_estoque.php"><strong class="text-dark">Ver na lista</strong>.</a>';
                header("Location: ../../view/produto/editar_produto.php?id=$id&tamanho=$tamanho");
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Erro ao verificar se o produto já está cadastrado!';
            throw new Exception('Erro ao verificar se o produto já está cadastrado!');
        }

        if (!is_numeric($tipo_id) || $tipo_id <= 0) {
            $_SESSION['error_message'] = 'Tipo de produto inváido!';
            header("Location: ../../view/estoque/listar_estoque.php");
            exit();
        }
        // Se houver arquivo na requisição
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $foto = $file['name'];
            $foto_tmp = $file['tmp_name'];

            $ext = pathinfo($foto, PATHINFO_EXTENSION);
            $foto = uniqid() . '.' . $ext;
            $destino = "../../view/produto/fotos/$foto";
            move_uploaded_file($foto_tmp, $destino);

            $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, foto = ?, tipo_id = ?, modificado_por = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsiii", $nome, $valor, $foto, $tipo_id, $adminId, $id);
        } else {
            $sql = "UPDATE produtos SET nome = ?, valor_unidade = ?, tipo_id = ?, modificado_por = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdiii", $nome, $valor, $tipo_id, $adminId, $id);
        }
    }
    // Se for pela tabela, deve preparar a instrução para atualizar somente valor e quantidade
    else {
        $sql = "UPDATE produtos SET valor_unidade = ?, modificado_por = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $valor, $adminId, $id);
    }

    if ($stmt->execute()) {
        $sql = "UPDATE estoque SET quantidade = ? WHERE tamanho = ? AND fk_produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $quantidade, $tamanho, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['success_message'] = 'Produto atualizado com sucesso!';
            $conn->commit();
            if ($atualizacaoCompleta) {
                // Redireciona para a página de edição se a atualização foi completa
                header("Location: ../../view/produto/editar_produto.php?id=$id&tamanho=$tamanho");
            } else {
                // Redireciona para a listagem de estoque caso contrário
                header("Location: ../../view/produto/listar_estoque.php");
            }
            exit();
        } else {
            $_SESSION['error_message'] = 'Não foi possível atualizar o produto! Erro ao atualizar a quantidade do estoque!';
            throw new Exception('Não foi possível atualizar o produto! Erro ao atualizar a quantidade do estoque!');
        }
    } else {
        $_SESSION['error_message'] = 'Não foi possível atualizar o produto!';
        throw new Exception('Não foi possível atualizar o produto! Erro ao atualizar a quantidade do estoque!');
    }
} catch (Exception $e) {
    $conn->rollback();

    registrar_log(
        $conn,
        'Erro ao atualizar produto',
        $e->getMessage(),
        $_SERVER['REQUEST_URI'],
        '../controller/produto_atualizar_produto.php',
        'produto'
    );

    if ($atualizacaoCompleta) {
        // Redireciona para a página de edição se a atualização foi completa
        header("Location: ../../view/produto/editar_produto.php?id=$id&tamanho=$tamanho");
    } else {
        // Redireciona para a listagem de estoque caso contrário
        header("Location: ../../view/estoque/listar_estoque.php");
    }
    exit();
}
