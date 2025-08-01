<?php
require_once 'conexao.php';

// CONSULTA CEPS NEGADOS
$filtro = $_GET['cep'] ?? '';
$ordenar = $_GET['ordenar'] ?? '';

$condicoes = [];
$params = [];

if (!empty($filtro)) {
    $condicoes[] = "cep LIKE ?";
    $params[] = "%" . $filtro . "%";
}

$orderBy = "ORDER BY ultima_tentativa DESC"; // padrão

if ($ordenar === 'tentativas_desc') {
    $orderBy = "ORDER BY tentativas DESC";
} elseif ($ordenar === 'tentativas_asc') {
    $orderBy = "ORDER BY tentativas ASC";
}

$sqlNegados = "SELECT cep, tentativas, ultima_tentativa FROM ceps_negados";
if ($condicoes) {
    $sqlNegados .= " WHERE " . implode(" AND ", $condicoes);
}
$sqlNegados .= " $orderBy";

$stmtNegados = $conn->prepare($sqlNegados);

if ($params) {
    $tipos = str_repeat("s", count($params));
    $stmtNegados->bind_param($tipos, ...$params);
}

$stmtNegados->execute();
$resultadoNegados = $stmtNegados->get_result();

$mensagem = "";

// EXCLUSÃO
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    $conn->query("DELETE FROM ceps_autorizados WHERE id = $idExcluir");
    header("Location: cadastrar_cep.php");
    exit;
}

// INSERÇÃO
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["acao"] === "salvar") {
    $cep = preg_replace('/\D/', '', $_POST["cep"]);

    // Verifica se já existe
    $check = $conn->prepare("SELECT id FROM ceps_autorizados WHERE cep = ?");
    $check->bind_param("s", $cep);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensagem = "❌ CEP já cadastrado!";
    } else {
        // Busca via BrasilAPI
        $res = @file_get_contents("https://brasilapi.com.br/api/cep/v2/$cep");
        if ($res !== false) {
            $dados = json_decode($res, true);
            $cidade = $dados['city'] ?? '';
            $estado = $dados['state'] ?? '';

            if ($cidade && $estado) {
                $stmt = $conn->prepare("INSERT INTO ceps_autorizados (cep, cidade, estado) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $cep, $cidade, $estado);
                if ($stmt->execute()) {
                    $mensagem = "✅ CEP cadastrado com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao salvar.";
                }
                $stmt->close();
            } else {
                $mensagem = "❌ Dados incompletos da BrasilAPI.";
            }
        } else {
            $mensagem = "❌ Erro ao consultar BrasilAPI.";
        }
    }
    $check->close();
}

// CONSULTA TODOS OS CEPS
$ceps = $conn->query("SELECT * FROM ceps_autorizados ORDER BY criado_em DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar CEP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 100%;
            padding: 2rem;
            background-color: #f8f9fa;
            color: #333;
        }


        h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #111;
        }



        a.nav-link:hover {
            text-decoration: underline;
        }

        .mensagem {
            background-color: #e0f7fa;
            color: #00796b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        form {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 10px 18px;
            background-color: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
            font-weight: 600;
            color: #222;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        td a {
            color: #c00;
            text-decoration: none;
            font-weight: bold;
        }

        td a:hover {
            text-decoration: underline;
        }

        .cep-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: space-between;
        }

        .cep-box {
            flex: 1 1 48%;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .cep-box {
                flex: 1 1 100%;
            }
        }
    </style>

</head>

<body>
    <?php include('header.php'); ?>

    <div class="cep-container">

        <!-- Cadastrar novo CEP -->
        <div class="cep-box">
            <h3>Cadastrar novo CEP</h3>
            <?php if ($mensagem): ?>
                <div class="mensagem"><?= $mensagem ?></div>
            <?php endif; ?>

            <form method="POST" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                <input type="text" name="cep" placeholder="Digite o CEP (ex: 01001000)" required
                    style="flex: 1; padding: 8px;" />
                <input type="hidden" name="acao" value="salvar" />
                <button type="submit" style="padding: 8px 16px;">Salvar</button>
            </form>

            <!-- tabela ceps autorizados -->
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f1f1;">
                        <th>CEP</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $ceps->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['cep'] ?></td>
                            <td><?= $row['cidade'] ?></td>
                            <td><?= $row['estado'] ?></td>
                            <td><a href="?excluir=<?= $row['id'] ?>" onclick="return confirm('Excluir este CEP?')">🗑️
                                    Excluir</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- CEPs Negados -->
        <div class="cep-box">
            <h3>CEPs Negados</h3>
            <form method="GET" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                <input type="text" name="cep" placeholder="Filtrar por CEP" value="<?= htmlspecialchars($filtro) ?>"
                    style="flex: 1; padding: 8px;" />
                <select name="ordenar" style="flex: 1; padding: 8px;">
                    <option value="">Ordenar por</option>
                    <option value="tentativas_desc" <?= $ordenar === 'tentativas_desc' ? 'selected' : '' ?>>Mais tentativas
                    </option>
                    <option value="tentativas_asc" <?= $ordenar === 'tentativas_asc' ? 'selected' : '' ?>>Menos tentativas
                    </option>
                </select>
                <button type="submit" style="padding: 8px 16px;">Filtrar</button>
            </form>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f1f1f1;">
                        <th>CEP</th>
                        <th>Tentativas</th>
                        <th>Última Tentativa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultadoNegados->num_rows > 0): ?>
                        <?php while ($row = $resultadoNegados->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['cep'] ?></td>
                                <td><?= $row['tentativas'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['ultima_tentativa'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhum CEP negado encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>


</html>