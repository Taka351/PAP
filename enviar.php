<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

try {
    // Instanciar o PHPMailer
    $mail = new PHPMailer(true);

    // Depuração (opcional)
    $mail->SMTPDebug = 0; // Defina 2 para depuração detalhada

    // Configuração do SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'memomedpap@gmail.com'; // Email da empresa MemoMed
    $mail->Password = 'scxw xujk vvev jejf'; // Senha de aplicativo gerada no Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuração do remetente fictício (no-reply)
    $mail->setFrom('no-reply@memomed.com', 'MemoMed - No Reply'); // Remetente fictício

    // Configuração do destinatário
    $mail->addAddress('memomedpap@gmail.com', 'MemoMed'); // Destinatário real

    // Conteúdo do e-mail
    $mail->Subject = "Teste de Envio - MemoMed";
    $mail->Body = "Este é um teste de envio de email com um remetente fictício (no-reply).";

    // Enviar o e-mail
    if ($mail->send()) {
        echo "Mensagem enviada com sucesso!";
        
    } else {
        echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";
    }
} catch (Exception $e) {
    echo "Erro ao enviar mensagem. Detalhes: {$e->getMessage()}";
}
?>
