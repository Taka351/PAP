<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

$sql = "SELECT name, email, created_at, telefone, morada FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Erro ao buscar informações do usuário.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = $_POST['telefone'] ?? null;
    $morada = $_POST['morada'] ?? null;

    $update_sql = "UPDATE users SET telefone = ?, morada = ? WHERE id = ?";
    $update_stmt = $mysqli->prepare($update_sql);
    $update_stmt->bind_param("ssi", $telefone, $morada, $user_id);

    if ($update_stmt->execute()) {
        $success_message = "Informações atualizadas com sucesso!";
        // Atualiza os dados exibidos
        $user['telefone'] = $telefone;
        $user['morada'] = $morada;
    } else {
        $error_message = "Erro ao atualizar informações: " . $update_stmt->error;
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/configuracoes.css">

</head>

<body>
<nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-logo">
                MemoMed
            </a>
            <div class="navbar-menu">
                <a href="menu.php" class="menu-link">Menu</a>
                <a href="sobre.php" class="menu-link">Sobre</a>
                <a href="suporte.php" class="menu-link">Suporte</a>
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="dropdown-button">
                        <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="caixa.php">Caixa</a>
                        <a href="alertas_agendados.php">Alertas</a>
                        <a href="configuracoes.php">Configurações</a>
                        <a href="logout.php">Sair</a>
                    </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2>Configurações da Conta</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="info">
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Data de Criação:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>

        <form method="POST" action="">
            <label for="telefone">Telefone:</label>
            <input type="tel" 
            id="telefone" 
            name="telefone" 
            pattern="9[1236]\d{7}"
            maxlength="9"
            placeholder="Digite seu telefone (9 dígitos)"
            title="Insira um número de telefone português válido com 9 dígitos"
            value="<?php echo htmlspecialchars($user['telefone']); ?>"
            required>


            <label for="morada">Morada:</label>
            <input type="text" id="morada" name="morada" placeholder="Digite sua morada" 
                   value="<?php echo htmlspecialchars($user['morada']); ?>">

            <button type="submit" class="primary-button">Atualizar Informações</button> 
        </form>
        
    </div>
    <script>
    function validarTelefone(input) {
    cdocument.getElementById('telefone').addEventListener('input', function() {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');
    
    // Limit to 9 digits
    if (this.value.length > 9) {
        this.value = this.value.slice(0, 9);
    }
    
    // Validate Portuguese mobile number format
    const isValid = /^9[1236]\d{7}$/.test(this.value);
    this.setCustomValidity(isValid ? '' : 'Insira um número válido começando com 91, 92, 93 ou 96');
});

    
</body>
</html>
