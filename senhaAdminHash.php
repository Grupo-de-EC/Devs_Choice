<?php
require_once 'conexao.php';

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$admins = [
    ['email' => 'shinigami@devschoice.com', 'senha' => 'pateta01'],
    ['email' => 'batatao@devschoice.com', 'senha' => 'pateta02'],
    ['email' => 'fantasmaretro@devschoice.com', 'senha' => 'pateta03'],
];

$query = "UPDATE users SET password = ? WHERE email = ? AND role = 'admin'";
$stmt = $conn->prepare($query);

foreach ($admins as $admin) {
    $senha_hash = password_hash($admin['senha'], PASSWORD_DEFAULT);
    $stmt->bind_param("ss", $senha_hash, $admin['email']);
    if ($stmt->execute()) {
        echo "Senha de {$admin['email']} atualizada com sucesso!<br>";
    } else {
        echo "Erro ao atualizar {$admin['email']}: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
?>
