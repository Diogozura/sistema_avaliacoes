<?php
// Habilita CORS
header("Access-Control-Allow-Origin: *");
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
