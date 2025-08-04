<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

$origens_permitidas = [
    'https://granja-fibra.vercel.app',
    'https://www.grajafibra.com.br',
    'http://localhost:3000',
];

if (in_array($origin, $origens_permitidas)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

require_once 'conexao.php'; // conexão separada

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');
$data = date('Y-m-d H:i:s');

// Validação simples
if (!$nome || !$telefone || !$email) {
    http_response_code(400);
    echo "Campos obrigatórios faltando.";
    exit;
}

// Inserir no banco
$stmt = $conn->prepare("INSERT INTO contatos (nome, telefone, email, mensagem, data_envio) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nome, $telefone, $email, $mensagem, $data);
$stmt->execute();

// Enviar e-mail
$to = "comercial@granjafibra.com"; 
$subject = "Novo Contato do Site";
$body = "Nome: $nome\nTelefone: $telefone\nE-mail: $email\nMensagem: $mensagem\nData: $data";
$headers = "From: contato@granjafibra.com"; 

mail($to, $subject, $body, $headers);

echo "ok";
?>
