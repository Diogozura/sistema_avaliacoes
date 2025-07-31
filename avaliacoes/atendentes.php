<?php
include('conexao.php');
include('gerar_qr.php');
date_default_timezone_set('America/Sao_Paulo');

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');

    if ($nome && $cpf) {
        // Verifica se CPF já existe
        $verifica = $conn->prepare("SELECT id FROM atendentes WHERE cpf = ?");
        $verifica->bind_param("s", $cpf);
        $verifica->execute();
        $verifica->store_result();

        if ($verifica->num_rows > 0) {
            $mensagem = "❌ Já existe um atendente com esse CPF.";
        } else {
            $stmt = $conn->prepare("INSERT INTO atendentes (nome, cpf) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome, $cpf);
            if ($stmt->execute()) {
                $id = $stmt->insert_id;

                // Gera o QR Code com base no ID
                $link = "http://localhost/avaliacoes/avaliar.php?atendente_id=$id";
                $arquivo = "qr_$id.png";
                $caminhoQR = gerarQRCode($link, $arquivo);

                // Salva caminho no banco
                $stmt2 = $conn->prepare("UPDATE atendentes SET qr_code_path = ? WHERE id = ?");
                $stmt2->bind_param("si", $caminhoQR, $id);
                $stmt2->execute();
                $stmt2->close();

                $mensagem = "✅ Atendente cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar: " . $stmt->error;
            }
            $stmt->close();
        }
        $verifica->close();
    } else {
        $mensagem = "❗ Nome e CPF são obrigatórios.";
    }
}

$atendentes = $conn->query("SELECT * FROM atendentes ORDER BY criado_em DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Atendentes</title>
    <meta name="robots" content="noindex, nofollow">

    <style>
         footer {
            margin-top: 30px;
            text-align: center;
            }
    </style>
</head>
<body>
  <?php include('header.php'); ?>

<div class="container mt-4">

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Cadastro de Atendentes</strong>
                </div>
                <div class="card-body">
                    <?php if ($mensagem): ?>
                        <div class="alert alert-info"><?= $mensagem ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" name="nome" id="nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF:</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" required placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Cadastrar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <strong>Atendentes Cadastrados</strong>
                </div>
                <div class="card-body table-responsive">
                    <?php if ($atendentes->num_rows > 0): ?>
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>QR Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $atendentes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nome']) ?></td>
                                    <td><?= htmlspecialchars($row['cpf']) ?></td>
                                    <td>
                                        <?php if ($row['qr_code_path']): ?>
                                            <img src="<?= $row['qr_code_path'] ?>" width="80" class="img-thumbnail mb-2"><br>
                                            <a href="<?= $row['qr_code_path'] ?>" download="qr_<?= $row['id'] ?>.png" class="btn btn-sm btn-outline-primary">📥 Baixar</a>
                                        <?php else: ?>
                                            <span class="text-muted">Sem QR</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Nenhum atendente cadastrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<footer>
      <p>&copy; 2025 - Avaliações QR Code</p>
    </footer>
</body>
</html>

