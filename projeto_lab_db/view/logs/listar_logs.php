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
    <meta charset="UTF-8">
    <title>Bartira Modas | Logs do Sistema</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="w-100 vh-100 d-flex flex-column justify-content-center align-items-center bg-dark p-3">
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 bg-light p-2 rounded shadow">
            <h2 class="text-center text-dark mb-3">Logs do Sistema</h2>

            <div class="mb-2 text-right">
                <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm">Voltar</a>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead class="thead-dark">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>