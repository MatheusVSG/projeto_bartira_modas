<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM logs ORDER BY data_criacao DESC");

if (!$result) {
    die("Erro ao executar consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../../head.php'; ?>
    <meta charset="UTF-8">
    <title>Bartira Modas | Logs do Sistema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-3">Logs do Sistema</h2>

            <div class="d-flex justify-content-end mb-2 gap-2">
                <!-- Botão de voltar já está fixo acima -->
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle rounded shadow-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Endereço</th>
                            <th>Link</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['titulo'] ?></td>
                                <td><?= $row['descricao'] ?></td>
                                <td><?= $row['endereco'] ?></td>
                                <td><?= $row['link'] ?></td>
                                <td><?= date("d/m/Y H:i:s", strtotime($row['data_criacao'])) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>