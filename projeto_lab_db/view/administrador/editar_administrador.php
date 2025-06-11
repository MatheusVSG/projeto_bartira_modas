<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para realizar esta ação.';
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'Não foi possível carregar os dados. Administrador não identificado.';
    header("Location: ../../view/administrador/listar_administrador.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM administrador WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    $_SESSION['error_message'] = 'Administrador não encontrado.';
    header("Location: ../../view/administrador/listar_administrador.php");
    exit;
}

$admin = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Editar Administrador</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../administrador/home_adm.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary'
            ],
            [
                'caminho' => 'listar_administrador.php',
                'titulo' => 'Administradores Cadastrados',
                'cor' => 'btn-primary'
            ]
        ];

        include '../../components/barra_navegacao.php';
        ?>

        <!-- Mensagens Sucesso/Erro -->
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
            Editar Administrador
        </h4>

        <div class="bg-light rounded p-4">
            <form action="../../controller/administrador/administrador_controller.php" method="POST" class="row">
                <input type="hidden" name="id" value="<?= htmlspecialchars($admin['id']) ?>">

                <div class="col-12 col-md-6 mb-3">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" name="usuario" id="usuario" class="form-control"
                        value="<?= htmlspecialchars($admin['usuario']) ?>" required>
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control"
                        placeholder="Deixe em branco para manter a senha atual">
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
                    <button type="reset" class="btn btn-warning">
                        Limpar
                    </button>

                    <button type="submit" name="editar" class="btn btn-success">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>