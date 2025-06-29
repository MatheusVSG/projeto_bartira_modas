<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'vendedor'])) {
    header("Location: ../../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Cliente</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        $linksAdicionais = [
            [
                'caminho' => $_SESSION['tipo_usuario'] == 'admin' ? '../administrador/home_adm.php' : '../vendedor/home_vendedor.php',
                'titulo' => 'Voltar ao Painel',
                'cor' => 'btn-secondary',
            ],

            [
                'caminho' => 'listar_clientes.php',
                'titulo' => 'Clientes Cadastrados',
                'cor' => 'btn-primary',
            ]
        ];

        include '../../components/barra_navegacao.php'
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
            Cadastro de Cliente
        </h4>

        <div class="bg-light rounded p-4">
            <form action=" ../../controller/cliente/salvar_cliente.php" method="POST" class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" id="nome" required placeholder="Digite o nome" class="form-control">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                    <input type="text" name="cpf" id="cpf" required pattern="\d{11}" title="Digite os 11 números do CPF, sem pontos ou traços"
                        oninput="this.value = this.value.replace(/\D/g, '')" maxlength="11" placeholder="Digite o CPF" class="form-control">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" required placeholder="Digite o e-mail" class="form-control">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="telefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" name="telefone" id="telefone" pattern="\d{11}" oninput="this.value = this.value.replace(/\D/g, '')" maxlength="11" required placeholder="Digite o telefone" class="form-control">
                </div>

                <div class="col-12 mb-3">
                    <p>Sexo <span class="text-danger">*</span></p>

                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input type="radio" name="sexo" value="M" id="Masc" required class="form-check-input">
                            <label for="Masc" class="form-check-label">Masculino</label>
                        </div>

                        <div class="form-check">
                            <input type="radio" name="sexo" value="F" id="Fem" required class="form-check-input">
                            <label for="Fem" class="form-check-label">Feminino</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-4 mb-lg-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" placeholder="Digite o logradouro" class="form-control">
                </div>

                <div class="col-12 col-lg-1 mb-4 mb-lg-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" maxlength="5" placeholder="Ex.: 10" class="form-control">
                </div>

                <div class="col-12 col-lg-3 mb-4 mb-lg-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Digite o bairro">
                </div>

                <div class="col-12 col-lg-3 mb-4 mb-lg-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Digite a cidade">
                </div>

                <div class="col-12 col-lg-1 mb-4 mb-lg-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select type="text" name="estado" id="estado" class="form-select">
                        <option value="">Selecione o Estado</option>
                        <?php
                        include '../../components/estados.php';
                        foreach ($estados as $estado) {
                        ?>
                            <option value="<?php echo $estado ?>"><?php echo $estado ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="reset" class="btn btn-warning">
                        Limpar
                    </button>

                    <button type="submit" class="btn btn-success">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>

</html>