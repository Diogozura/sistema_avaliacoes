<?php
require_once 'conexao.php';

$filtro = '';
$where = '';
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

$sql = "SELECT cep, tentativas, ultima_tentativa FROM ceps_negados";
if ($condicoes) {
  $sql .= " WHERE " . implode(" AND ", $condicoes);
}
$sql .= " $orderBy";

$stmt = $conn->prepare($sql);

if ($params) {
  $tipos = str_repeat("s", count($params));
  $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>CEPs Negados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: auto;
            padding: 2rem;
            background-color: #f8f9fa;
            color: #333;
        }

        h2 {
            margin-bottom: 1rem;
            text-align: center;
            color: #111;
        }

        form {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        label {
            font-weight: bold;
            min-width: 120px;
        }

        input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 8px 16px;
            background-color: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
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

        td[colspan="3"] {
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>
    
<?php include('header.php'); ?>
    <h2>CEPs Negados</h2>

    <form method="GET">
        <label for="cep">Filtrar por CEP:</label>
        <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($filtro) ?>" placeholder="Ex: 12345678">
        <label for="ordenar">Ordenar por:</label>
        <select id="ordenar" name="ordenar">
            <option value="">Padrão</option>
            <option value="tentativas_desc" <?= ($_GET['ordenar'] ?? '') === 'tentativas_desc' ? 'selected' : '' ?>>Mais
                tentativas</option>
            <option value="tentativas_asc" <?= ($_GET['ordenar'] ?? '') === 'tentativas_asc' ? 'selected' : '' ?>>Menos
                tentativas</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>CEP</th>
                <th>Tentativas</th>
                <th>Última Tentativa</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
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
</body>

</html>