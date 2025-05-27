<?php
session_start();
include_once '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID do cliente não fornecido.";
    exit;
}


$id = $_GET['id'];
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    echo "Cliente não encontrado.";
    exit;
}

$cliente = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Editar Cliente</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">

        <div class="col-12 col-sm-8 col-md-6 col-lg-5 bg-light p-3 rounded shadow">
            <h2 class="text-center text-dark mb-3">Editar Cliente</h2>

            <form action="../../controller/cliente/atualizar_cliente.php" method="POST">
                <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

                <div class="mb-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control form-control-sm" required value="<?= htmlspecialchars($cliente['nome']) ?>">
                </div>

                <div class="mb-2">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control form-control-sm" required pattern="\d{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')" value="<?= htmlspecialchars($cliente['cpf']) ?>">
                </div>

                <div class="mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" required value="<?= htmlspecialchars($cliente['email']) ?>">
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="listar_clientes.php" class="btn btn-secondary btn-sm">Voltar</a>
                    <button type="submit" class="btn btn-success btn-sm">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>