<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

include_once 'conexao.php';

// Consulta para contar alertas ativos
$query_alertas = "SELECT COUNT(*) as total_alertas 
                 FROM alerta a 
                 JOIN compartimento c ON a.refidcompartimento = c.idcompartimento
                 JOIN caixa cx ON c.ref_idcaixa = cx.idcaixa
                 WHERE cx.ref_user = ?";
$stmt_alertas = $mysqli->prepare($query_alertas);
$stmt_alertas->bind_param("i", $_SESSION['id']);
$stmt_alertas->execute();
$result_alertas = $stmt_alertas->get_result();
$total_alertas = $result_alertas->fetch_assoc()['total_alertas'];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/menu.css">


</head>

<body>
   <!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="#" class="navbar-logo">MemoMed</a>
        <div class="navbar-menu">
            <a href="menu.php" class="menu-link">Menu</a>
            <a href="sobre.php" class="menu-link">Sobre</a>
            <a href="suporte.php" class="menu-link">Suporte</a><!-- Link correto para a página ajuda.php -->
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


    <main class="main-content">
    <section class="hero-section">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p class="hero-subtitle">Organize a sua medicação de forma inteligente e segura</p>
    </section>

    <section class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-pills"></i>
        </div>
        <h2>Gestão de Medicamentos</h2>
        <p>Mantenha um registo detalhado da sua medicação diária com facilidade e precisão.</p>
        <a href="caixa.php" class="card-link">Iniciar</a>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-bell"></i>
        </div>
        <h2>Sistema de Avisos</h2>
        <p>Receba lembretes personalizados para nunca falhar uma toma importante.</p>
        <a href="caixa.php" class="card-link">Definir Avisos</a>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h2>Análise e Controlo</h2>
        <p>Acompanhe a evolução do seu tratamento através de estatísticas pormenorizadas.</p>
        <a href="alertas_agendados.php" class="card-link">Consultar Dados</a>
    </div>
</section>

<section class="info-section">
    <div class="info-container">
        <div class="info-text">
            <h2>Funcionamento</h2>
            <div class="step-list">
                <div class="step">
                    <span class="step-number">1</span>
                    <p>Adicione a sua medicação à lista pessoal</p>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <p>Defina o plano de tomas adequado</p>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <p>Siga as notificações diárias</p>
                </div>
            </div>
        </div>
        <div class="info-image">
    <img src="images/caixamedicamentos.jpg" alt="Caixa organizadora de medicamentos" width="400">
</div>
    </div>
</section>

<section class="stats-section">
    <div class="stat-card">
        <h3>Medicamentos Registados</h3>
        <p class="stat-number">0</p>
    </div>
    <div class="stat-card">
        <h3>Notificações Ativas</h3>
        <p class="stat-number"><?php echo $total_alertas; ?></p>
    </div>
    <div class="stat-card">
        <h3>Período de Acompanhamento</h3>
        <p class="stat-number">0</p>
    </div>
</section>