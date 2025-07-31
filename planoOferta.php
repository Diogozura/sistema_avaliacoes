<?php
require_once 'conexao.php';

// Caminhos dos arquivos
$arquivoPlanos = 'planos.json';
$arquivoOfertas = 'ofertas.json';

// Salvar novos dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tipo = $_POST['tipo'];
  $json = $_POST['json'];

  $dados = json_decode($json, true);

  if (!$dados) {
    $erro = "JSON inválido!";
  } else {
    $arquivo = $tipo === 'planos' ? $arquivoPlanos : $arquivoOfertas;
    file_put_contents($arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $mensagem = "Dados de $tipo salvos com sucesso!";
  }
}

// Carregar os dados atuais
$planos = file_exists($arquivoPlanos) ? file_get_contents($arquivoPlanos) : '';
$ofertas = file_exists($arquivoOfertas) ? file_get_contents($arquivoOfertas) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>CMS Planos e Ofertas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 900px;
      margin: auto;
      padding: 2rem;
      background: #f5f5f5;
    }

    h2 {
      margin-top: 2rem;
    }

    textarea {
      width: 100%;
      height: 300px;
      margin-bottom: 1rem;
      font-family: monospace;
      font-size: 14px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: #fff;
    }

    button {
      padding: 10px 20px;
      border: none;
      background-color: #111;
      color: white;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
    }

    .mensagem {
      margin-top: 1rem;
      font-weight: bold;
      color: green;
    }

    .erro {
      color: red;
    }
  </style>
</head>
<body>
  <h1>CMS: Editar Planos e Ofertas</h1>

  <?php if (isset($mensagem)) echo "<div class='mensagem'>$mensagem</div>"; ?>
  <?php if (isset($erro)) echo "<div class='mensagem erro'>$erro</div>"; ?>

  <form method="POST">
    <h2>Planos</h2>
    <textarea name="json"><?= htmlspecialchars($planos) ?></textarea>
    <input type="hidden" name="tipo" value="planos">
    <button type="submit">Salvar Planos</button>
  </form>

  <form method="POST">
    <h2>Ofertas</h2>
    <textarea name="json"><?= htmlspecialchars($ofertas) ?></textarea>
    <input type="hidden" name="tipo" value="ofertas">
    <button type="submit">Salvar Ofertas</button>
  </form>
</body>
</html>
