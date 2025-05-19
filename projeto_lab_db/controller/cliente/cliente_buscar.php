<?php
include '../../connection.php';

$termo = $_GET['termo'] ?? '';

$sql = "SELECT id, nome, cpf FROM clientes WHERE nome LIKE '%$termo%' LIMIT 10";
$result = mysqli_query($conn, $sql);

$clientes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $clientes[] = $row;
}

echo json_encode($clientes);
