<?php
include('conexao.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Preencha todos os campos!";
    } else {
        // Validar o formato do e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Formato de email inválido!";
        } else {
            $email = $mysqli->real_escape_string($email);
            $sql_code = "SELECT * FROM users WHERE email = '$email'";
            $sql_query = $mysqli->query($sql_code);

            if ($sql_query->num_rows === 1) {
                $user = $sql_query->fetch_assoc();
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    header("Location: menu.php");
                    exit();
                } else {
                    $error = "Email ou palavra-passe incorretos.";
                }
            } else {
                $error = "Utilizador não encontrado.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sessão - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/index.css">

   
</head>
<body>
    <div class="container">
        <div class="welcome-side">
            <h1>Bem-vindo ao MemoMed</h1>
            <p>Faça a gestão dos seus medicamentos com facilidade e segurança.</p>
        </div>
        <div class="form-side">
            <h2 class="form-title">Iniciar Sessão</h2>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" novalidate>
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="input-field" 
                               placeholder="Digite o seu email" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Palavra-passe</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="input-field" 
                               placeholder="Digite a sua palavra-passe" required>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Entrar</button>
            </form>
            <p class="redirect-text">Não tem uma conta? <a href="criar.php">Criar conta</a></p>
        </div>
    </div>
</body>
</html>