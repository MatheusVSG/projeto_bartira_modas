<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM administrador WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <div class="d-flex justify-content-end mb-2 gap-2">
            <a href="listar_administrador.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar</a>
        </div>
        <h1 class="text-center text-warning mb-4">Editar Administrador</h1>

        <form action="../../controller/administrador/administrador_controller.php" method="POST" class="bg-light text-dark p-4 rounded shadow">
            <input type="hidden" name="id" value="<?= $admin['id'] ?>">

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuário</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?= $admin['usuario'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" placeholder="Nova senha" required>
            </div>

            <div class="d-flex justify-content-start gap-2 mt-3">
                <button type="submit" name="editar" class="btn btn-success">Atualizar</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>
