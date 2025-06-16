<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    $_SESSION['error_message'] = 'Acesso negado. Você não tem permissão para realizar esta ação.';
    header("Location: ../../");
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'Não foi possível carregar os dados. Cliente não identificado.';
    header("Location: ../../view/cliente/listar_clientes.php");
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

include '../../components/estados.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Editar Cliente</title>
</head>

<body>
    <div class="w-100 min-vh-100 justify-content-center align-items-center bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../../' : '../vendedor/home_vendedor.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary'
            ],
            [
                'caminho' => 'listar_clientes.php',
                'titulo' => 'Clientes Cadastrados',
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
            Editar Cliente
        </h4>

        <div class="bg-light p-4 rounded">
            <form action="../../controller/cliente/atualizar_cliente.php" method="POST" class="row">
                <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

                <div class="col-12 col-lg-4 mb-3">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" id="nome" class="form-control form-control-sm" required value="<?= htmlspecialchars($cliente['nome']) ?>">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                    <input type="text" name="cpf" id="cpf" class="form-control form-control-sm" required pattern="\d{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')" value="<?= htmlspecialchars($cliente['cpf']) ?>">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control form-control-sm" required value="<?= htmlspecialchars($cliente['email']) ?>">
                </div>
                <div class="col-12 col-lg-2 mb-3">
                    <label for="telefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" name="telefone" id="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')" maxlength="11" placeholder="Digite o telefone" class="form-control" required>
                </div>

                <!--<div class="col-12 mb-3">
                    <p>Sexo</p>

                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input type="radio" name="sexo" value="M" id="Masc" class="form-check-input">
                            <label for="Masc" class="form-check-label">Masculino</label>
                        </div>

                        <div class="form-check">
                            <input type="radio" name="sexo" value="F" id="Fem" class="form-check-input">
                            <label for="Fem" class="form-check-label">Feminino</label>
                        </div>
                    </div>
                </div>-->

                <div class="col-12 col-lg-4 mb-4 mb-lg-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" value="<?= htmlspecialchars($cliente['logradouro']) ?>" placeholder="Digite o logradouro" class="form-control">
                </div>

                <div class="col-12 col-lg-1 mb-4 mb-lg-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" value="<?= htmlspecialchars($cliente['numero']) ?>" maxlength="5" placeholder="Ex.: 10" class="form-control">
                </div>

                <div class="col-12 col-lg-3 mb-4 mb-lg-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($cliente['bairro']) ?>" class="form-control" placeholder="Digite o bairro">
                </div>

                <div class="col-12 col-lg-3 mb-4 mb-lg-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($cliente['cidade']) ?>" class="form-control" placeholder="Digite a cidade">
                </div>

                <div class="col-12 col-lg-1 mb-4 mb-lg-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select type="text" name="estado" id="estado" value="<?= htmlspecialchars($cliente['estado']) ?>" class="form-select">
                        <option value="">Selecione o Estado</option>
                        <?php
                        foreach ($estados as $estado) {
                        ?>
                            <option value="<?php echo $estado ?>" <?php $estado === $cliente['estado'] ? print('selected') : '' ?>>
                                <?php echo $estado ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">

                    <a href="listar_clientes.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>