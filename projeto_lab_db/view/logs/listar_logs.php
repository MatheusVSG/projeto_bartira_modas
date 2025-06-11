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

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Logs do Sistema</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Logs do Sistema</h4>

            <div class="table-responsive mt-3">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Endereço</th>
                                <th></th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $row['id'] ?></td>
                                    <td data-label="Título"><?= htmlspecialchars($row['titulo']) ?></td>
                                    <td data-label="Descrição"><?= htmlspecialchars($row['descricao']) ?></td>
                                    <td data-label="Endereço"><?= htmlspecialchars($row['endereco']) ?></td>
                                    <td data-label="Link"><?= htmlspecialchars($row['link']) ?></td>
                                    <td data-label="Data"><?= date("d/m/Y H:i:s", strtotime($row['data_criacao'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results mt-3">Nenhum log encontrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>