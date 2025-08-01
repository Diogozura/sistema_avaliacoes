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