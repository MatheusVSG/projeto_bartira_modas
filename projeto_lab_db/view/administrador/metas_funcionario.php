<?php
session_start();
include_once '../../connection.php';
include_once '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Bartira Modas | Definir Meta</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => '../administrador/home_adm.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],
            [
                'caminho' => 'listar_metas.php',
                'titulo' => 'Metas Ativas',
                'cor' => 'btn-primary',
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
            Definir Nova Meta
        </h4>

        <div class="bg-light rounded p-4">
            <form method="POST" action="../../controller/administrador/processar_meta_controller.php" class="row">
                <input type="hidden" name="cadastrar_meta">

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label">Vendedor</label>
                    <select name="vendedor_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php
                        $vendedor_selecionado = isset($_GET['vendedor_id']) ? $_GET['vendedor_id'] : '';
                        $res = mysqli_query($conn, "SELECT id, nome FROM vendedores ORDER BY nome");
                        while ($v = mysqli_fetch_assoc($res)) {
                            $selected = ($v['id'] == $vendedor_selecionado) ? 'selected' : '';
                            echo "<option value='{$v['id']}' {$selected}>{$v['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label">Valor da Meta (R$)</label>
                    <input type="number" name="meta_valor" step="0.01" min="0" class="form-control" required>
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label">Data de Validade</label>
                    <input type="date" name="data_validade" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
                    <button type="reset" class="btn btn-warning">
                        Limpar
                    </button>

                    <button type="submit" class="btn btn-success">
                        Cadastrar Meta
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>