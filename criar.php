<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido.";
    } elseif (strlen($password) < 8 || !$uppercase || !$lowercase || !$number) {
        $error = "A senha não atende aos requisitos mínimos.";
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Este email já está cadastrado.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hash);

            if ($stmt->execute()) {
                header("Location: index.php");
                exit();
            } else {
                $error = "Erro ao criar conta. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3a7e6e, #57d7b5);
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            display: flex;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .welcome-side {
            flex: 1;
            background: linear-gradient(135deg, #3a7e6e, #2e5f4f);
            padding: 40px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .welcome-side h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .welcome-side p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .form-side {
            flex: 1;
            padding: 40px;
            background: #fff;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .input-wrapper {
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #3a7e6e;
            outline: none;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            user-select: none;
        }

        .requirements {
            margin-top: 8px;
            font-size: 0.85em;
            color: #666;
        }

        .requirement {
            margin: 4px 0;
            display: flex;
            align-items: center;
        }

        .requirement.valid {
            color: #3a7e6e;
        }

        .requirement::before {
            content: "•";
            margin-right: 8px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #3a7e6e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #57d7b5;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #3a7e6e;
            text-decoration: none;
            font-weight: 500;
        }

        .error-message {
            background: #ffe5e5;
            color: #ff3333;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .welcome-side {
                padding: 30px;
            }

            .form-side {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-side">
            <h1>Bem-vindo ao MemoMed</h1>
            <p>Gerencie seus medicamentos com facilidade e segurança. Crie sua conta agora mesmo!</p>
        </div>
        <div class="form-side">
            <h2 class="form-title">Criar Conta</h2>
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="" novalidate>
                <div class="input-group">
                    <label for="name">Nome Completo</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" class="input-field" 
                               placeholder="Digite seu nome completo" required
                               value="<?php echo htmlspecialchars($name ?? ''); ?>">
                    </div>
                </div>
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="input-field" 
                               placeholder="Digite seu e-mail" required
                               value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="input-field" 
                               placeholder="Digite sua senha" required>
                        <span class="password-toggle" onclick="togglePassword()">👁️</span>
                    </div>
                    <div class="requirements">
                        <div class="requirement" id="length">Mínimo de 8 caracteres</div>
                        <div class="requirement" id="uppercase">Uma letra maiúscula</div>
                        <div class="requirement" id="lowercase">Uma letra minúscula</div>
                        <div class="requirement" id="number">Um número</div>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Criar Conta</button>
            </form>
            <p class="login-link">Já tem uma conta? <a href="index.php">Entrar</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }

        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };

            for (let req in requirements) {
                const element = document.getElementById(req);
                if (requirements[req]) {
                    element.classList.add('valid');
                } else {
                    element.classList.remove('valid');
                }
            }
        });
    </script>
</body>
</html>
