<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

include_once 'conexao.php';

if (!$mysqli) {
    die("Erro: Conexão com o banco de dados não foi estabelecida.");
}

$mensagem = "";
$ref_user = $_SESSION['id'];

// Verificar se o usuário já registrou uma caixa
$query_check = "SELECT idcaixa, modelo, num_compartimentos FROM caixa WHERE ref_user = ?";
$stmt_check = $mysqli->prepare($query_check);
$stmt_check->bind_param("i", $ref_user);
$stmt_check->execute();
$result = $stmt_check->get_result();

$caixa_registrada = null;
if ($result->num_rows > 0) {
    $caixa_registrada = $result->fetch_assoc();
}

// Processar o formulário de registro da caixa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$caixa_registrada) { // Permitir registro apenas se nenhuma caixa estiver registrada
        $modelo = $_POST['modelo'] ?? null;
        $num_compartimentos = $_POST['num_compartimentos'] ?? null;

        if ($modelo && $num_compartimentos) {
            $query = "INSERT INTO caixa (modelo, num_compartimentos, ref_user) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sii", $modelo, $num_compartimentos, $ref_user);

            if ($stmt->execute()) {
                // Obter o ID da caixa recém-criada
                $caixa_id = $mysqli->insert_id;
                
                // Chamar a API para registrar a caixa
                $apiKey = "XRP01233TT8912K";
                $caixaId = $modelo; // Usando o modelo como ID da caixa
                
                $apiUrl = "https://bluecloud.pt/esag_apoioalunos/api_caixas/InserirCaixaCompartimento.php?ApiKey={$apiKey}&Caixa={$caixaId}";
                
                // Tentar fazer a chamada de API
                $result = @file_get_contents($apiUrl);
                
                $mensagem = "Caixa registada com sucesso! Modelo: $modelo, Número de compartimentos: $num_compartimentos";
                $caixa_registrada = [
                    'idcaixa' => $caixa_id,
                    'modelo' => $modelo, 
                    'num_compartimentos' => $num_compartimentos
                ]; // Atualizar o registro da caixa
            } else {
                $mensagem = "Erro ao registar a caixa: " . $stmt->error;
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos.";
        }
    } else {
        $mensagem = "Você já possui uma caixa registrada. Não é possível registrar outra.";
    }
}

// Verificar se o número de compartimentos foi registrado
$compartimentos = $caixa_registrada ? $caixa_registrada['num_compartimentos'] : 0;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/caixa.css">

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
                <!-- End User Dropdown -->
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Conectar Caixa</h1>
    
    <?php if ($caixa_registrada): ?>
        <!-- Exibir informações da caixa registrada -->
        <p>Você já tem uma caixa registrada:</p>
        <ul>
            <li><strong>Modelo:</strong> <?php echo htmlspecialchars($caixa_registrada['modelo']); ?></li>
            <li><strong>Número de Compartimentos:</strong> <?php echo htmlspecialchars($caixa_registrada['num_compartimentos']); ?></li>
        </ul>
        
        <?php if(isset($_GET['erro']) && $_GET['erro'] == '2'): ?>
            <div class="mensagem-erro">
                Já existe um alerta agendado para este compartimento.
            </div>
        <?php endif; ?>
        
        <div class="compartimentos-grid">
            <?php for ($i = 1; $i <= $caixa_registrada['num_compartimentos']; $i++): ?>
            <div class="compartimento-card">
                <h3>Compartimento <?php echo $i; ?></h3>

                <?php
                // Buscar o ID do compartimento do banco de dados
                $query_comp = "SELECT idcompartimento FROM compartimento 
                              WHERE ref_idcaixa = ? AND descricao_compartimento = ?";
                $descricao_comp = "Compartimento " . $i;
                $stmt_comp = $mysqli->prepare($query_comp);
                $stmt_comp->bind_param("is", $caixa_registrada['idcaixa'], $descricao_comp);
                $stmt_comp->execute();
                $result_comp = $stmt_comp->get_result();
                $compartimento_id = ($result_comp->num_rows > 0) ? $result_comp->fetch_assoc()['idcompartimento'] : null;
                
                // Código de verificação do alerta se o compartimento existir
                if ($compartimento_id) {
                    $query_alerta = "SELECT a.datahora, a.repeticao, c.comprimidos 
                                    FROM alerta a 
                                    JOIN compartimento c ON a.refidcompartimento = c.idcompartimento 
                                    WHERE a.refidcompartimento = ?";
                    $stmt_alerta = $mysqli->prepare($query_alerta);
                    $stmt_alerta->bind_param("i", $compartimento_id);
                    $stmt_alerta->execute();
                    $result_alerta = $stmt_alerta->get_result();
                    $alerta = $result_alerta->fetch_assoc();

                    if ($alerta) {
                        echo '<div class="alerta-info">';
                        echo '<p>Medicamento: ' . htmlspecialchars($alerta['comprimidos']) . '</p>';
                        echo '<p>Alerta configurado para: ' . date('d/m/Y H:i', strtotime($alerta['datahora'])) . '</p>';
                        echo '<p>Repetição: ' . htmlspecialchars($alerta['repeticao']) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
                <form method="POST" action="salvar_alerta.php">
                    <input type="hidden" name="compartimento_id" value="<?php echo $i; ?>">

                    <div class="form-group">
                        <label for="comprimido_<?php echo $i; ?>">Nome do Medicamento:</label>
                        <input type="text" 
                               id="comprimido_<?php echo $i; ?>" 
                               name="comprimidos" 
                               class="form-input medicamento-input" 
                               autocomplete="off"
                               required>
                        <div id="sugestoes_<?php echo $i; ?>" class="sugestoes-lista"></div>
                    </div>

                    <div class="form-group">
                        <label for="hora_<?php echo $i; ?>">Hora:</label>
                        <input type="time" id="hora_<?php echo $i; ?>" name="datahora" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="repeticao_<?php echo $i; ?>">Repetição:</label>
                        <select id="repeticao_<?php echo $i; ?>" name="repeticao" class="form-input" required>
                            <option value="Diário">Diário</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Mensal">Mensal</option>
                            <option value="Nenhum">Nenhum</option>
                        </select>
                    </div>

                    <button type="submit" class="config-button">Salvar Alerta</button>
                </form>
            </div>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <!-- Mostrar formulário de registro se nenhuma caixa estiver registrada -->
        <p>Utilize a câmera do seu dispositivo ou introduza o código da caixa manualmente.</p>
    </div>

    <?php if ($mensagem): ?>
    <p class="mensagem">
        <?php echo $mensagem; ?>
    </p>
    <?php endif; ?>

    <form method="POST" action="caixa.php">
        <label for="modelo">Modelo da Caixa:</label>
        <input type="text" id="modelo" name="modelo" placeholder="Exemplo: QR_001" pattern="QR_[0-9]{3}" required>

        <label for="num_compartimentos">Número de Compartimentos:</label>
        <select id="num_compartimentos" name="num_compartimentos" required>
            <option value="" disabled selected>Selecione</option>
            <option value="4">4 Compartimentos</option>
            <option value="6">6 Compartimentos</option>
        </select>

        <button type="submit" class="primary-button">Registar Caixa</button>
    </form>
    <?php endif; ?>
    </div>
</body>
</html>
