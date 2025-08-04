<?php
include('auth.php');
include('conexao.php');
date_default_timezone_set('America/Sao_Paulo');
$periodo = $_GET['periodo'] ?? 'semana';

// Consulta todos os interessados
$resultado = $conn->query("SELECT nome, telefone, criado_em FROM usuarios_interessados ORDER BY criado_em DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Usuários Interessados</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 800px;
      margin: auto;
      padding: 2rem;
      background-color: #f9f9f9;
      color: #333;
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      font-weight: 600;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    td[colspan="3"] {
      text-align: center;
      color: #888;
    }
  </style>
</head>
<body>
<?php include('header.php'); ?>
  <h2>Usuários Interessados</h2>

  <table>
    <thead>
      <tr>
        <th>Nome</th>
        <th>Telefone</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($resultado->num_rows > 0): ?>
        <?php while ($row = $resultado->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= htmlspecialchars($row['telefone']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['criado_em'])) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="3">Nenhum usuário interessado registrado ainda.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>
