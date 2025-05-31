<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../login.php");
    exit();
}

$mensagem_sucesso = '';
if (isset($_GET['excluido']) && $_GET['excluido'] == 1) {
    $mensagem_sucesso = 'Cliente excluído com sucesso!';
} elseif (isset($_GET['atualizado']) && $_GET['atualizado'] == 1) {
    $mensagem_sucesso = 'Cliente atualizado com sucesso!';
}

$linksAdicionais = [
    [
        'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary'
    ],
    [
        'caminho' => 'cadastro_cliente.php',
        'titulo' => 'Novo Cliente',
        'cor' => 'btn-primary'
    ]
];

$sql = "SELECT * FROM clientes ORDER BY nome";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Lista de Clientes</title>
    <style>
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
        
        /* Bordas arredondadas */
        .custom-table tbody tr:first-child td:first-child { border-top-left-radius: 8px; }
        .custom-table tbody tr:first-child td:last-child { border-top-right-radius: 8px; }
        .custom-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
        .custom-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }
        
        /* Efeitos hover */
        .custom-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Botões de ação */
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
        
        /* Mensagens */
        .no-results {
            padding: 20px;
            text-align: center;
            color: #adb5bd;
            font-style: italic;
        }
        
        /* Layout responsivo */
        @media (max-width: 992px) {
            .custom-table thead { display: none; }
            
            .custom-table tbody tr {
                display: block;
                margin-bottom: 15px;
            }
            
            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 15px;
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
                gap: 8px;
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .action-btn {
                padding: 4px 10px;
                font-size: 12px;
            }
            
            .custom-table tbody td {
                font-size: 14px;
                padding: 8px 10px;
            }
            
            .custom-table tbody td::before {
                font-size: 13px;
                margin-right: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php include '../../../components/barra_navegacao.php'; ?>

        <h4 class="text-warning mb-0">
            Lista de Clientes
        </h4>

        <?php if (!empty($mensagem_sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= htmlspecialchars($mensagem_sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Email</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cliente = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td data-label="ID"><?= $cliente['id'] ?></td>
                                    <td data-label="Nome"><?= htmlspecialchars($cliente['nome']) ?></td>
                                    <td data-label="CPF"><?= htmlspecialchars($cliente['cpf']) ?></td>
                                    <td data-label="Email"><?= htmlspecialchars($cliente['email']) ?></td>
                                    <td data-label="Ações">
                                        <div class="action-buttons">
                                            <a href="editar_cliente.php?id=<?= $cliente['id'] ?>" class="action-btn btn-edit">Editar</a>
                                            <a href="../../controller/cliente/excluir_cliente.php?id=<?= $cliente['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">Excluir</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">Nenhum cliente encontrado.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>