<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['excluido']) && $_GET['excluido'] == 1) {
    $mensagem_sucesso = 'Vendedor excluído com sucesso!';
} elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1) {
    $mensagem_sucesso = 'Vendedor atualizado com sucesso!';
}

$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_vendedor.php',
        'titulo' => 'Novo Vendedor',
        'cor' => 'btn-primary'
    ]
];

$sql = "SELECT * FROM vendedores ORDER BY nome";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Lista de Vendedores</title>
    <style>

        .responsive-container {
            width: 100%;
            padding: 0 15px;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: transparent;
        }
        
        .custom-table thead th {
            background-color: #343a40;
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
            border: none;
            position: sticky;
            top: 0;
        }
        
        .custom-table tbody tr {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .custom-table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border: none;
            color: #f8f9fa;
        }
        
        .custom-table tbody tr:first-child td:first-child { border-top-left-radius: 8px; }
        .custom-table tbody tr:first-child td:last-child { border-top-right-radius: 8px; }
        .custom-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
        .custom-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }
        
        .custom-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .action-btn {
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 14px;
            font-weight: 500;
            margin-right: 5px;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn-edit { background-color: #ffc107; color: #212529; }
        .btn-delete { background-color: #dc3545; color: white; }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .badge-admin { background-color: rgba(40, 167, 69, 0.2); color: #28a745; }
        .badge-vendedor { background-color: rgba(108, 117, 125, 0.2); color: #adb5bd; }
        
        .no-results {
            padding: 20px;
            text-align: center;
            color: #adb5bd;
            font-style: italic;
        }
        
        @media (max-width: 992px) {
            .custom-table thead { display: none; }
            
            .custom-table tbody tr {
                display: block;
                margin-bottom: 15px;
                padding: 10px;
            }
            
            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 10px;
                text-align: right;
            }
            
            .custom-table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 15px;
                color: #ffc107;
            }
            
            .action-buttons {
                display: flex;
                justify-content: flex-end;
                gap: 5px;
            }
        }
        
        @media (max-width: 576px) {
            .action-btn {
                padding: 4px 8px;
                font-size: 12px;
            }
            
            .custom-table tbody td {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">
                Lista de Vendedores
            </h4>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive mt-3">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vendedor = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $vendedor['id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($vendedor['nome']) ?></td>
                                    <td data-label="CPF"><?= htmlspecialchars($vendedor['cpf']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($vendedor['email']) ?></td>
                                    <td data-label="Tipo">
                                        <span class="status-badge <?= $vendedor['tipo_usuario'] === 'admin' ? 'badge-admin' : 'badge-vendedor' ?>">
                                            <?= $vendedor['tipo_usuario'] === 'admin' ? 'Administrador' : 'Vendedor' ?>
                                        </span>
                                    </td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_vendedor.php?id=<?= $vendedor['id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/vendedor/excluir_vendedor.php?id=<?= $vendedor['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">Nenhum vendedor cadastrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>