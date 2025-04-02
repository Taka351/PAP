<?php
session_start();

// Incluir o ficheiro de configurações
include('config.php');

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

$erro = '';
$sucesso = '';

// Verificar o último envio de mensagem
$ultimo_envio = $_SESSION['ultimo_envio'] ?? 0;

// Recuperar os dados do utilizador
$user_email = $_SESSION['email'] ?? 'Email não definido';
$user_phone = $_SESSION['telefone'] ?? '';
$user_name = $_SESSION['name']; // Nome do utilizador logado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = $_POST['mensagem'] ?? '';

    // Definir tempo limite de 10 minutos entre envios
    $tempo_atual = time();
    $tempo_limite = 0; // 10 minutos em segundos

    if (($tempo_atual - $ultimo_envio) < $tempo_limite) {
        $tempo_restante = $tempo_limite - ($tempo_atual - $ultimo_envio);
        $erro = "Aguarde " . ceil($tempo_restante / 60) . " minutos para enviar outra mensagem.";
    } elseif (empty($mensagem)) {
        $erro = "Por favor, preencha a mensagem.";
    } else {
        try {
            // Instanciar o PHPMailer
            $mail = new PHPMailer(true);

            // Configuração do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'memomedpap@gmail.com';
            $mail->Password = 'scxw xujk vvev jejf'; // Senha do Gmail (deve ser protegida)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Definir remetente
            $mail->setFrom('no-reply@memomed.com', 'MemoMed - No Reply');

            // Definir destinatário
            $mail->addAddress('memomedpap@gmail.com', 'MemoMed');

            // Definir conteúdo do email
            $telefone_formatado = !empty($user_phone) ? $user_phone : "Utilizador não informou telefone.";

            // Melhorar o corpo do email com HTML
            $mail->isHTML(true); // Habilitar envio de email em formato HTML
            $mail->Subject = "Pedido de Ajuda - MemoMed";
            $mail->Body = "
                <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
                            .header { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 10px; }
                            .content { margin-bottom: 20px; }
                            .footer { font-size: 12px; color: #777; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>Pedido de Ajuda - MemoMed</div>
                            <div class='content'>
                                <p><strong>Nome:</strong> $user_name</p>
                                <p><strong>Email:</strong> $user_email</p>
                                <p><strong>Telefone:</strong> $telefone_formatado</p>
                                <p><strong>Mensagem:</strong></p>
                                <p>$mensagem</p>
                            </div>
                            <div class='footer'>
                                Este email foi enviado automaticamente pelo sistema MemoMed.
                            </div>
                        </div>
                    </body>
                </html>";

            // Enviar o email
            if ($mail->send()) {
                $sucesso = "Mensagem enviada com sucesso!";
                $_SESSION['ultimo_envio'] = time();
            } else {
                $erro = "Erro ao enviar mensagem: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $erro = "Erro ao enviar mensagem. Detalhes: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda - MemoMed</title>
    <link rel="stylesheet" href="CSS/suporte.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-logo">MemoMed</a>
            <div class="navbar-menu">
                <a href="menu.php" class="menu-link">Menu</a>
                <a href="sobre.php" class="menu-link">Sobre</a>
                <a href="suporte.php" class="menu-link">Suporte</a>
                <div class="dropdown">
                    <button class="dropdown-button"><?php echo htmlspecialchars($_SESSION['name']); ?></button>
                    <div class="dropdown-content">
                        <a href="caixa.php">Caixa</a>
                        <a href="alertas_agendados.php">Alertas</a>
                        <a href="configuracoes.php">Configurações</a>
                        <a href="logout.php">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content container">
        <h1>Precisa de Ajuda?</h1>
        <p>Envie-nos a sua dúvida ou problema, e entraremos em contacto consigo.</p>

        <?php if (!empty($erro)): ?>
            <div class="error-message"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="success-message"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return validarFormulario()">
            <!-- O campo de nome foi removido -->
            <p><strong>Enviando como:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>

            <label for="mensagem">Mensagem:</label>
            <textarea id="mensagem" name="mensagem" rows="5" placeholder="Escreva a sua mensagem..." required></textarea>

            <button type="submit">Enviar Mensagem</button>
        </form>
    </main>

    <script>
        function validarFormulario() {
            var mensagem = document.getElementById("mensagem").value;
            if (mensagem.length < 60) {
                alert("A mensagem deve ter pelo menos 60 caracteres.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
