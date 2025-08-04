<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

$origens_permitidas = [
    'https://granja-fibra.vercel.app',
    'https://www.grajafibra.com.br',
    'http://localhost:3000'
];

if (in_array($origin, $origens_permitidas)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Conexão

include('conexao.php');
date_default_timezone_set('America/Sao_Paulo');
$periodo = $_GET['periodo'] ?? 'semana';

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
