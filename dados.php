<?php
// Habilita CORS
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

// Arquivos JSON
$arquivoPlanos = 'planos.json';
$arquivoOfertas = 'ofertas.json';

// Lê os arquivos
$planos = file_exists($arquivoPlanos) ? json_decode(file_get_contents($arquivoPlanos), true) : [];
$ofertas = file_exists($arquivoOfertas) ? json_decode(file_get_contents($arquivoOfertas), true) : [];

// Retorna o JSON unificado
echo json_encode([
    'planos' => $planos,
    'ofertas' => $ofertas
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
