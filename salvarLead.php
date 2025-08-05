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

// Recebe JSON
$dados = json_decode(file_get_contents("php://input"), true);

// Valida
if (!$dados || !isset($dados['nome'], $dados['telefone'], $dados['cep'], $dados['numero'], $dados['plano'], $dados['utm_source'])) {
    echo json_encode(["sucesso" => false, "erro" => "Dados incompletos"]);
    exit;
}

$nome = $dados['nome'];
$telefone = $dados['telefone'];
$cep = $dados['cep'];
$numero = $dados['numero'];
$plano = $dados['plano'];
$utm_source = $dados['utm_source'] ?? 'organico';

// Prepara e salva
$stmt = $conn->prepare("INSERT INTO usuarios_interessados (nome, telefone, cep, numero, plano, utm_source) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nome, $telefone, $cep, $numero, $plano,  $utm_source);

// Mensagem e link configurável
$mensagem = "Olá! Me chamo {$nome} e tenho interesse em assinar a internet.";
$config = include('config.php');
$link = str_replace('{mensagem}', urlencode($mensagem), $config['whatsapp_url'] ?? '');

if ($stmt->execute()) {
    echo json_encode([
        "sucesso" => true,
        "redirect_url" => $link
    ]);
} else {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Erro ao salvar"
    ]);
}

$stmt->close();
$conn->close();
