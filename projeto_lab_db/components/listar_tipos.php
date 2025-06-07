<?php
try {
    include '../../connection.php';
    include '../../controller/logs/logger.controller.php';

    $tipos = [];
    $sql = "SELECT * FROM tipos_produto ORDER BY nome";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        while ($tipo = mysqli_fetch_assoc($result)) {
            array_push($tipos, $tipo);
        }
    } else {
        throw new Exception("Nenhum tipo de roupa encontrado.");
    }
} catch (Exception $e) {
    registrar_log(
        $conn,
        'Erro ao listar tipos de roupa',
        $e->getMessage(), // Usa a mensagem do erro diretamente
        $_SERVER['REQUEST_URI'],
        'components/listar_tipos.php'
    );
    exit;
}
