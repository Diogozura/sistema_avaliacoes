<?php
include('auth.php');
include('conexao.php');
date_default_timezone_set('America/Sao_Paulo');
$periodo = $_GET['periodo'] ?? 'semana';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Avaliações</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <meta name="robots" content="noindex, nofollow">

  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    main { padding: 30px; max-width: 1000px; margin: auto; }
    h1 { font-size: 26px; margin-bottom: 20px; }

    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .tab-button {
      padding: 10px 20px;
      background-color: #e0e0e0;
      border: none;
      cursor: pointer;
      border-radius: 6px;
      font-weight: bold;
    }

    .tab-button.active {
      background-color: #007bff;
      color: #fff;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .filtros button {
      padding: 8px 15px;
      margin: 5px;
      background-color: #007bff;
      border: none;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    .filtros button:hover {
      background-color: #0056b3;
    }

    canvas {
      background: white;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 10px;
      width: 100%;
    }

    .total {
      margin-top: 15px;
      font-size: 16px;
      font-weight: bold;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    footer {
      margin-top: 30px;
      text-align: center;
    }
  </style>
</head>
<body>
  <?php include('header.php'); ?>

  <main>
    <h1>📈 Relatório de Avaliações</h1>

    <div class="tabs">
      <button class="tab-button active" onclick="openTab(event, 'graficoTab')">📊 Gráfico</button>
      <button class="tab-button" onclick="openTab(event, 'rankingTab')">🏆 Ranking</button>
    </div>

    <div id="graficoTab" class="tab-content active">
      <div class="filtros">
        <button onclick="carregarGrafico('semana')">Últimos 7 dias</button>
        <button onclick="carregarGrafico('mes')">Últimos 30 dias</button>
        <button onclick="carregarGrafico('ano')">Últimos 12 meses</button>
        <button onclick="carregarGrafico('todos')">Todos</button>
      </div>
      <canvas id="grafico" height="300"></canvas>
      <div class="total" id="totalGeral">Total Geral: ...</div>
    </div>

    <div id="rankingTab" class="tab-content">
      <h2>Ranking de Atendentes</h2>
      <table>
        <thead>
          <tr>
            <th>Atendente</th>
            <th>Total de Avaliações</th>
          </tr>
        </thead>
        <tbody id="rankingBody"></tbody>
      </table>
      <p class="total">Total geral de avaliações: <span id="totalAvaliacoes">0</span></p>
    </div>

    <footer>
      <p>&copy; 2025 - Avaliações QR Code</p>
    </footer>
  </main>

  <script>
    let myChart;

    function carregarGrafico(periodo = 'semana') {
      fetch(`get_relatorio.php?periodo=${periodo}`)
        .then(res => res.json())
        .then(data => {
          const labels = data.avaliacoes.map(a => a.nome);
          const valores = data.avaliacoes.map(a => a.total);

          if (myChart) {
            myChart.data.labels = labels;
            myChart.data.datasets[0].data = valores;
            myChart.update();
          } else {
            const ctx = document.getElementById('grafico').getContext('2d');
            myChart = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  label: 'Avaliações por Atendente',
                  data: valores,
                  backgroundColor: '#007bff',
                  borderRadius: 5
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: { display: false },
                  title: {
                    display: true,
                    text: 'Avaliações por Período'
                  }
                },
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                  }
                }
              }
            });
          }

          document.getElementById('totalGeral').textContent = `Total Geral: ${data.totalGeral}`;

          // Ranking
          const rankingBody = document.getElementById('rankingBody');
          rankingBody.innerHTML = '';
          data.avaliacoes.forEach(atendente => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${atendente.nome}</td><td>${atendente.total}</td>`;
            rankingBody.appendChild(row);
          });
          document.getElementById('totalAvaliacoes').textContent = data.totalGeral;
        });
    }

    function openTab(evt, tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
      document.getElementById(tabId).classList.add('active');
      evt.target.classList.add('active');
    }

    carregarGrafico('semana');
  </script>
</body>
</html>
