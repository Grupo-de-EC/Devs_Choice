<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "devs_choice", 3307);
$graficoData = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

if (!$conn->connect_error) {
    $sql = "SELECT estrelas, COUNT(*) as quantidade FROM feedbacks GROUP BY estrelas";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $estrela = (int)$row['estrelas'];
        $graficoData[$estrela] = (int)$row['quantidade'];
    }
    $conn->close();
}
$graficoJS = json_encode(array_values($graficoData));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel do Administrador</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-container">
      <a href="#" class="nav-brand">Painel do Admin</a>
      <ul class="nav-links">
        <li><a href="#questionarios">Questionários</a></li>
        <li><a href="#feedbacks">Feedbacks</a></li>
        <li><a href="#atualizacao">Atualizar IA</a></li>
        <li><a href="#grafico">Gráfico</a></li>
        <li><a href="#perfil">Perfil</a></li>
        <li><a href="login.html">Sair</a></li>
      </ul>
    </div>
  </nav>

  <main class="container">
    <section id="questionarios">
      <h2>Gerenciar Questionários</h2>
      <button onclick="editarQuestionario()">Editar Questionários</button>
    </section>

    <section id="feedbacks">
      <h2>Feedbacks dos Usuários</h2>
      <ul id="lista-feedbacks">
        <?php
          // Conexão com o banco
          $conn = new mysqli("localhost", "root", "", "devs_choice", 3307);
          if ($conn->connect_error) {
              echo "<li>Erro ao conectar com o banco de dados.</li>";
          } else {
              $sql = "SELECT comentario, estrelas, data_envio FROM feedbacks ORDER BY data_envio DESC LIMIT 5";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      $estrelas = str_repeat("⭐", (int)$row['estrelas']);
                      echo "<li><strong>{$estrelas}</strong> - " . htmlspecialchars($row['comentario']) . "</li>";
                  }
              } else {
                  echo "<li>Nenhum feedback ainda.</li>";
              }
              $conn->close();
          }
        ?>
      </ul>
    </section>

    <section id="atualizacao">
      <h2>Atualização da IA</h2>
      <button onclick="atualizarIA()">Executar Atualização</button>
    </section>

    <section id="grafico">
      <h2>Gráfico de Satisfação</h2>
      <canvas id="graficoSatisfacao" width="600" height="300"></canvas>
    </section>

    <section id="perfil">
      <h2>Perfil do Administrador</h2>
      <p>Nome: <?php echo $_SESSION['name']; ?></p>
      <p>Email: <?php echo $_SESSION['email']; ?></p>
      <button onclick="editarPerfil()">Editar Perfil</button>
    </section>
  </main>

  <footer>
    &copy; 2025 - Painel de Administração
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const dadosEstrelas = <?php echo $graficoJS; ?>;
</script>
<script src="admin.js" defer></script>
</body>
</html>
