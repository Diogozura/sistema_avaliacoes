<?php
require_once 'conexao.php'; // sua conexão com o banco

$periodo = $_GET['periodo'] ?? 'semana';

switch ($periodo) {
    case 'semana':
        $dataInicio = date('Y-m-d H:i:s', strtotime('-7 days'));
        break;
    case 'mes':
        $dataInicio = date('Y-m-d H:i:s', strtotime('-30 days'));
        break;
    case 'ano':
        $dataInicio = date('Y-m-d H:i:s', strtotime('-1 year'));
        break;
    case 'todos':
    default:
        $dataInicio = '1970-01-01 00:00:00';
        break;
}

$sql = "
    SELECT a.nome, COUNT(v.id) AS total
    FROM atendentes a
    LEFT JOIN avaliacoes v ON a.id = v.atendente_id AND v.data_hora >= ?
    GROUP BY a.id, a.nome
    ORDER BY total DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dataInicio);
$stmt->execute();
$result = $stmt->get_result();

$avaliacoes = [];
$totalGeral = 0;

while ($row = $result->fetch_assoc()) {
    $avaliacoes[] = [
        'nome' => $row['nome'],
        'total' => (int)$row['total']
    ];
    $totalGeral += (int)$row['total'];
}

echo json_encode([
    'avaliacoes' => $avaliacoes,
    'totalGeral' => $totalGeral
]);
