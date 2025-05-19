<?php
include '../../connection.php';

$termo = $_GET['termo'] ?? '';
$sql = "SELECT id, nome, valor_unidade FROM produtos WHERE nome LIKE ?";
$stmt = $conn->prepare($sql);
$busca = "%$termo%";
$stmt->bind_param("s", $busca);
$stmt->execute();
$result = $stmt->get_result();

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($produtos);
