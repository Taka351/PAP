<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/sobre.css">

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
                    <button class="dropdown-button"><?php echo htmlspecialchars($_SESSION['name']); ?></button>
                    <div class="dropdown-content">
                        <a href="caixa.php">Caixa</a>
                        <a href="alertas_agendados.php">Alertas</a>
                        <a href="configuracoes.php">Configurações</a>
                        <a href="logout.php">Sair</a>
                    </div>
                </div>
                <!-- End User Dropdown -->
            </div>
        </div>
    </nav>

    <div class="background-animation">
        <div class="wave"></div>
        <div class="wave" style="animation-delay: 2s;"></div>
        <div class="wave" style="animation-delay: 4s;"></div>
    </div>

    <div class="container">
        <h1>Sobre a MemoMed</h1>
        <p>
            A MemoMed é uma solução inovadora projetada para revolucionar a forma como as pessoas gerem a sua medicação diária. 
            A nossa plataforma combina tecnologia avançada com simplicidade de uso, tornando o controlo da toma de medicamentos mais eficiente e seguro.
        </p>

        <h2>As Nossas Funcionalidades</h2>
        <div class="feature-grid">
            <div class="feature-item">
                <i class="fas fa-clock feature-icon"></i>
                <h3>Lembretes Inteligentes</h3>
                <p>Alertas personalizados para cada medicamento</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-box feature-icon"></i>
                <h3>Caixa Inteligente</h3>
                <p>Organização eficaz dos medicamentos</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-bell feature-icon"></i>
                <h3>Notificações</h3>
                <p>Avisos visuais e sonoros</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-cog feature-icon"></i>
                <h3>Fácil Configuração</h3>
                <p>Interface intuitiva e amigável</p>
            </div>
        </div>

        <h2>A Nossa Missão</h2>
        <p>
            O nosso compromisso é melhorar a qualidade de vida das pessoas, especialmente idosos, 
            através de uma solução tecnológica que garante o uso correto e pontual dos medicamentos. 
            Acreditamos que a tecnologia deve ser uma aliada na promoção da saúde e do bem-estar.
        </p>
    </div>
</body>
</html>
