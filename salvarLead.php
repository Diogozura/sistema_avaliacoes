<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Conexão
require_once 'conexao.php'; // já conecta com $conn

// Recebe JSON
$dados = json_decode(file_get_contents("php://input"), true);

// Valida
if (!$dados || !isset($dados['nome'], $dados['telefone'], $dados['cep'], $dados['numero'])) {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos"]);
    exit;
}

$nome = $dados['nome'];
$telefone = $dados['telefone'];
$cep = $dados['cep'];
$numero = $dados['numero'];

// Prepara e salva
$stmt = $conn->prepare("INSERT INTO usuarios_interessados (nome, telefone, cep, numero) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $telefone, $cep, $numero);

if ($stmt->execute()) {
    echo json_encode(["sucesso" => true]);
} else {
    echo json_encode(["sucesso" => false, "erro" => "Erro ao salvar"]);
}

$stmt->close();
$conn->close();
