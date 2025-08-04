<!-- header.php -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 70px;
      /* Para não cobrir o conteúdo */
      background-color: #f8f9fa;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.3rem;
    }

    .nav-link {
      margin-left: 15px;
      font-weight: 500;
    }

    .nav-link.text-danger {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <!-- Navbar fixa no topo -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container">
      <p class="navbar-brand">Avaliações QR Code</p>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="relatorio.php">Relatório</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="atendentes.php">Atendentes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="gerador_utm.php">gerador_utm</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="planoOferta.php">plano oferta</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="usuarios_interessados.php">usuarios interessados</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="cadastrar_cep.php">CEPs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Sair</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>