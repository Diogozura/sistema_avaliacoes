<?php
include 'conexao.php';

$id = $_GET['atendente_id'] ?? null;

if ($id && is_numeric($id)) {
    // Grava a avaliação com fuso horário BR
    date_default_timezone_set('America/Sao_Paulo');
    $stmt = $conn->prepare("INSERT INTO avaliacoes (atendente_id, data_hora) VALUES (?, NOW())");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redireciona para uma página de obrigado ou link do Google
header("Location: https://www.google.com/");
exit;
