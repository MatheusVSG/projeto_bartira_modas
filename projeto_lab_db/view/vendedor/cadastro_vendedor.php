<?php
session_start();
include_once '../../connection.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include '../../head.php'; ?>
    <title>Bartira Modas | Cadastro de Vendedor</title>
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
                'caminho' => 'listar_vendedores.php',
                'titulo' => 'Vendedores cadastrados',
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

        <h4 class="text-warning mb-0">
            Cadastro de Vendedor
        </h4>

        <div class="bg-light rounded p-4">
            <form action="../../controller/vendedor/salvar_vendedor.php" method="POST" class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="nome" id="nome" required class="form-control" placeholder="Digite o nome">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                    <input type="text" name="cpf" id="cpf" required class="form-control" pattern="\d{11}" maxlength="11"
                        oninput="this.value = this.value.replace(/\D/g, '')" title="Digite exatamente 11 números, somente dígitos."
                        placeholder="00000000000">
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" required placeholder="Digite o e-mail" class="form-control">
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label for="telefone" class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" name="telefone" id="telefone" class="form-control" pattern="\d{11}"
                        oninput="this.value = this.value.replace(/\D/g, '')" maxlength="11" required placeholder="Digite o telefone">
                </div>

                <div class="col-12 mb-3">
                    <p>Sexo <span class="text-danger">*</span></p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check">
                            <input type="radio" name="sexo" value="M" id="Masc" class="form-check-input" required>
                            <label for="Masc" class="form-check-label">Masculino</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sexo" value="F" id="Fem" class="form-check-input" required>
                            <label for="Fem" class="form-check-label">Feminino</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="logradouro" class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control" placeholder="Digite o logradouro">
                </div>

                <div class="col-12 col-lg-1 mb-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" class="form-control" maxlength="5" placeholder="Ex.: 10">
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Digite o bairro">
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Digite a cidade">
                </div>

                <div class="col-12 col-lg-1 mb-3">
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

                <div class="col-12 col-lg-3 mb-3">
                    <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                    <input type="password" name="senha" id="senha" required class="form-control" placeholder="Digite a senha">
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-3">
                    <button type="reset" class="btn btn-warning">Limpar</button>
                    <button type="submit" name="cadastrar" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>