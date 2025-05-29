<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {

    header("Location: ../../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM produtos WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Editar Produto</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="d-flex justify-content-end mb-2 gap-2">
            <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar ao Painel</a>
            <a href="../logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>

        <h1 class="text-center text-warning mb-5">Editar Produto</h1>

        <form action="../../controller/atualizar_produto.php" method="post" enctype="multipart/form-data" class="bg-light text-dark p-4 rounded shadow-sm">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($row['nome']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tipo_id" class="form-label">Tipo de Produto:</label>
                <select name="tipo_id" id="tipo_id" class="form-control" required>
                    <option value="">Selecione o tipo</option>
                    <?php
                    $tipos = $conn->query("SELECT id, nome FROM tipos_produto");
                    while ($tipo = $tipos->fetch_assoc()) {
                        $selected = ($row['tipo_id'] == $tipo['id']) ? 'selected' : '';
                        echo "<option value='{$tipo['id']}' $selected>{$tipo['nome']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="valor_unidade" class="form-label">Valor da Unidade:</label>
                <input type="number" step="0.01" name="valor_unidade" id="valor_unidade" value="<?= $row['valor_unidade'] ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Nova Foto (opcional):</label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Atual:</label><br>
                <img src="fotos/<?= $row['foto'] ?>" width="120" class="rounded border">
            </div>

            <div class="d-flex justify-content-between">
                <a href="cadastro-produto.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Produto</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Voltar</button>
            </div>

        </form>
    </div>
</body>

</html>