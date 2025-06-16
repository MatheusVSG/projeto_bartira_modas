<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Apenas administradores podem acessar esta página.';
    header("Location: ../../login.php");
    exit();
}

$sql = "SELECT * FROM forma_pagto ORDER BY descricao";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Formas de Pagamento</title>
</head>

<body>
    <div class="w-100 vh-100 d-flex flex-column bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary'
            ],
            [
                'caminho' => 'cadastrar_forma_pagto.php',
                'titulo' => 'Nova Forma de Pagamento',
                'cor' => 'btn-primary'
            ]
        ];

        include '../../components/barra_navegacao.php';
        ?>

        <div class="position-fixed top-0 end-0 z-3 p-3">
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['success_message']);
            }
            ?>

            <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php
                unset($_SESSION['error_message']);
            }
            ?>
        </div>

        <h4 class="text-warning">
            Formas de Pagamento
        </h4>

        <div class="flex-grow-1 overflow-y-hidden">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="h-100 overflow-y-auto table-responsive">
                    <table class="custom-table">
                        <thead class="position-sticky top-0 start-0 z-2">
                            <tr>
                                <th>ID</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($forma = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $forma['id'] ?></td>
                                    <td data-label="Descrição"><?= htmlspecialchars($forma['descricao']) ?></td>
                                    
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">Nenhuma forma de pagamento cadastrada.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>