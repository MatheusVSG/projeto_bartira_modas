<?php
session_start();
include '../../connection.php';
include '../../head.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Cadastro de Vendedor</title>
    <style>
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <h1 class="text-center text-warning">Cadastro de Vendedor</h1>


        <form action="../../controller/vendedor/salvar_vendedor.php" method="post" class="bg-light text-dark p-4 rounded shadow">
            <input type="text" name="nome" class="form-control mb-2" placeholder="Nome" required>
            <input type="text" name="cpf" class="form-control mb-2" placeholder="CPF" required class="form-control form-control-sm" required class="form-control form-control-sm" pattern="\d{11}" maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')" title="Digite exatamente 11 números, somente dígitos.">
            <input type="text" name="email" class="form-control mb-2" placeholder="Email" required>
            <input type="text" name="telefone" class="form-control mb-2" placeholder="Telefone">
            <input type="text" name="logradouro" class="form-control mb-2" placeholder="Logradouro">
            <input type="text" name="numero" class="form-control mb-2" placeholder="Número">
            <input type="text" name="bairro" class="form-control mb-2" placeholder="Bairro">
            <input type="text" name="cidade" class="form-control mb-2" placeholder="Cidade">
            <input type="text" name="estado" class="form-control mb-2" placeholder="Estado">
            <select name="sexo" class="form-control mb-2">
                <option value="">Sexo</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
            </select>
            <input type="password" name="senha" class="form-control mb-2" placeholder="Senha" required>
            <button type="submit" class="btn btn-success">Salvar</button>

        </form>

        <h2 class="text-warning mt-5">Lista de Vendedores</h2>


        <table class="table table-striped table-dark mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $res = $conn->query("SELECT * FROM vendedores");
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nome']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <a href='editar_vendedor.php?id={$row['id']}' class='btn btn-warning btn-sm'>Editar</a>
                            <a href='../../controller/vendedor/excluir_vendedor.php?id={$row['id']}' onclick='return confirm(\"Tem certeza?\")' class='btn btn-danger btn-sm'>Excluir</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="../administrador/home_adm.php" class="btn btn-secondary btn-sm position-fixed" style="top: 24px; right: 24px; z-index: 999;">Voltar ao Painel</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous" defer></script>
</body>

</html>