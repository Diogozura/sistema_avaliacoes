<?php
require_once 'conexao.php'; // sua conexão com o banco
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Função auxiliar para resposta rápida
function resposta($autorizado, $mensagem = "", $dados = []) {
  echo json_encode([
    "autorizado" => $autorizado,
    "mensagem" => $mensagem,
    "dados" => $dados
  ]);
  exit;
}

// Recebe o CEP via GET
$cep = isset($_GET['cep']) ? preg_replace('/\D/', '', $_GET['cep']) : null;

if (!$cep || strlen($cep) !== 8) {
  resposta(false, "CEP inválido");
}


// Verifica se está autorizado
$stmt = $conn->prepare("SELECT cidade, estado FROM ceps_autorizados WHERE cep = ?");
$stmt->bind_param("s", $cep);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  // Autorizado
  resposta(true, "✅ Atendemos sua região", [
    "cidade" => $row['cidade'],
    "estado" => $row['estado']
  ]);
}

// Se não autorizado, registra no ceps_negados
$check = $conn->prepare("SELECT id FROM ceps_negados WHERE cep = ?");
$check->bind_param("s", $cep);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  // Já existe -> atualiza tentativas
  $update = $conn->prepare("UPDATE ceps_negados SET tentativas = tentativas + 1 WHERE cep = ?");
  $update->bind_param("s", $cep);
  $update->execute();
  $update->close();
} else {
  // Novo -> insere
  $insert = $conn->prepare("INSERT INTO ceps_negados (cep) VALUES (?)");
  $insert->bind_param("s", $cep);
  $insert->execute();
  $insert->close();
}

// Retorna resposta negativa
resposta(false, "❌ Não atendemos esse CEP no momento");
