<?php
include('auth.php');
include('conexao.php');
date_default_timezone_set('America/Sao_Paulo');
$periodo = $_GET['periodo'] ?? 'semana';

$configFile = 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoLink = $_POST['whatsapp_url'] ?? '';
    $novoLink = trim($novoLink);
    
    if ($novoLink !== '') {
        $config = "<?php\nreturn [\n  'whatsapp_url' => '" . addslashes($novoLink) . "'\n];";
        file_put_contents($configFile, $config);
        $mensagem = "Link atualizado com sucesso!";
    } else {
        $mensagem = "Por favor, insira um link válido.";
    }
}

// Carrega o valor atual
$config = include($configFile);
$whatsapp_url = $config['whatsapp_url'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerador de Link com UTM</title>
    <style>
        body {
            font-family: Arial;
            padding: 2rem;
        }

        input,
        select,
        textarea {
            margin: 8px 0;
            width: 100%;
            padding: 8px;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <h2>Gerador de Link UTM</h2>

    <form method="get" onsubmit="gerarLink(event)">
        <label>URL base (ex: https://seudominio.com/assinar)</label>
        <input type="text" id="base" placeholder="https://seudominio.com/assinar" required>

        <label>utm_source</label>
        <input type="text" id="source" placeholder="facebook, whatsapp, google" required>

        <label>utm_medium</label>
        <input type="text" id="medium" placeholder="cpc, social, mensagem" required>

        <label>utm_campaign</label>
        <input type="text" id="campaign" placeholder="verao2025, blackfriday" required>

        <label>utm_content (opcional)</label>
        <input type="text" id="content" placeholder="botao1, banner_topo">

        <button type="submit">Gerar Link</button>
    </form>

    <h3>Link Gerado:</h3>
    <textarea id="resultado" rows="3" readonly></textarea>

    <h2>Editar link de envio Assinatura</h2>
    <form method="POST">
        <label for="whatsapp_url">Link com placeholder <code>{mensagem}</code>:</label><br>
        <input type="text" name="whatsapp_url" value="<?= htmlspecialchars($whatsapp_url) ?>"
            style="width: 100%; max-width: 600px;" required />
        <br><br>
        <button type="submit">Salvar</button>
    </form>
    <script>
        function gerarLink(e) {
            e.preventDefault();
            const base = document.getElementById('base').value;
            const source = document.getElementById('source').value;
            const medium = document.getElementById('medium').value;
            const campaign = document.getElementById('campaign').value;
            const content = document.getElementById('content').value;

            const url = new URL(base);
            url.searchParams.set('utm_source', source);
            url.searchParams.set('utm_medium', medium);
            url.searchParams.set('utm_campaign', campaign);
            if (content) url.searchParams.set('utm_content', content);

            document.getElementById('resultado').value = url.toString();
        }
    </script>
</body>

</html>