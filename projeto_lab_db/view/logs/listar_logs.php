<?php
// Inicia a sessão para controle de usuário logado
session_start();

// Inclui o arquivo de conexão com o banco de dados
include '../../connection.php';

// Inclui o cabeçalho HTML (head), que provavelmente tem links CSS, meta tags etc.
include '../../head.php';

// Verifica se o usuário está logado e se é do tipo 'admin'
// Caso contrário, redireciona para a página de login
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Executa uma consulta para buscar todos os registros da tabela 'logs', ordenando pela data mais recente
$result = mysqli_query($conn, "SELECT * FROM logs ORDER BY data_criacao DESC");

// Se a consulta falhar, interrompe o script e mostra mensagem de erro
if (!$result) {
    die("Erro ao executar consulta: " . mysqli_error($conn));
}

// Define um array com links adicionais para navegação (ex: botão voltar ao painel)
$linksAdicionais = [
    [
        'caminho' => '../administrador/home_adm.php',
        'titulo' => 'Voltar ao Painel',
        'cor' => 'btn-secondary' // classe CSS do botão
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php
    // Inclui novamente o arquivo head para carregar estilos, scripts, favicon etc.
    include '../../head.php';
    ?>
    <title>Bartira Modas | Logs do Sistema</title>
</head>

<body>
    <div class="w-100 min-vh-100 bg-dark px-3 pb-3">
        <?php
        // Inclui a barra de navegação (menu) do sistema
        include '../../components/barra_navegacao.php';
        ?>

        <div class="responsive-container">
            <h4 class="text-warning mb-0">Logs do Sistema</h4>

            <div class="table-responsive mt-3">
                <?php if (mysqli_num_rows($result) > 0): // Se há registros no resultado 
                ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Endereço</th>
                                <th></th> <!-- Coluna sem título, provavelmente para o link -->
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Loop para percorrer todos os registros da consulta
                            while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                <tr>
                                    <!-- Exibe os dados em cada coluna da tabela -->
                                    <td data-label="ID"><?= $row['id'] ?></td>
                                    <td data-label="Título"><?= htmlspecialchars($row['titulo']) ?></td> <!-- htmlspecialchars para evitar XSS -->
                                    <td data-label="Descrição"><?= htmlspecialchars($row['descricao']) ?></td>
                                    <td data-label="Endereço"><?= htmlspecialchars($row['endereco']) ?></td>
                                    <td data-label="Link"><?= htmlspecialchars($row['link']) ?></td>
                                    <!-- Formata a data para o formato brasileiro dia/mês/ano hora:minuto:segundo -->
                                    <td data-label="Data"><?= date("d/m/Y H:i:s", strtotime($row['data_criacao'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: // Caso não haja registros 
                ?>
                    <div class="no-results mt-3">Nenhum log encontrado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php
// Fecha a conexão com o banco de dados para liberar recursos
mysqli_close($conn);
?>