<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

include_once 'conexao.php';

$ref_user = $_SESSION['id'];
$alertas_combinados = [];

// Obter informações da caixa do usuário
$query_caixa = "SELECT modelo FROM caixa WHERE ref_user = ?";
$stmt_caixa = $mysqli->prepare($query_caixa);
$stmt_caixa->bind_param("i", $ref_user);
$stmt_caixa->execute();
$caixa_info = $stmt_caixa->get_result()->fetch_assoc();

// Buscar alertas do banco de dados
$query_db = "SELECT a.idalerta, a.datahora, a.repeticao, c.descricao_compartimento, c.comprimidos 
            FROM alerta a 
            JOIN compartimento c ON a.refidcompartimento = c.idcompartimento
            JOIN caixa cx ON c.ref_idcaixa = cx.idcaixa
            WHERE cx.ref_user = ?
            ORDER BY a.datahora ASC";

$stmt_db = $mysqli->prepare($query_db);
$stmt_db->bind_param("i", $ref_user);
$stmt_db->execute();
$alertas_db = $stmt_db->get_result();

while ($alerta = $alertas_db->fetch_assoc()) {
    $alertas_combinados[] = $alerta;
}

// Buscar alertas da API
if ($caixa_info) {
    $apiKey = "XRP01233TT8912K";
    $modelo_caixa = $caixa_info['modelo'];
    $apiUrl = "https://bluecloud.pt/esag_apoioalunos/api_caixas/LerCaixaCompartimentos.php?ApiKey={$apiKey}&Caixa={$modelo_caixa}";
    
    $apiResponse = @file_get_contents($apiUrl);
    
    if ($apiResponse !== false) {
        $apiData = json_decode($apiResponse, true);
        
        if (isset($apiData['compartimentos'])) {
            foreach ($apiData['compartimentos'] as $compartimento) {
                // Verificar se já existe no banco de dados
                $exists = false;
                foreach ($alertas_combinados as $dbAlert) {
                    if ($dbAlert['descricao_compartimento'] == 'Compartimento '.$compartimento['compartimento']) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $alertas_combinados[] = [
                        'idalerta' => 'api_'.$compartimento['compartimento'],
                        'datahora' => date('Y-m-d H:i:s', strtotime($compartimento['hora'])),
                        'repeticao' => $compartimento['repeticao'] ?? 'Diário',
                        'descricao_compartimento' => 'Compartimento '.$compartimento['compartimento'],
                        'comprimidos' => $compartimento['medicamento'] ?? 'Medicamento API',
                        'origem' => 'api'
                    ];
                }
            }
        }
    }
}

// Ordenar alertas por horário
usort($alertas_combinados, function($a, $b) {
    return strtotime($a['datahora']) - strtotime($b['datahora']);
});

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas Agendados - MemoMed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/alerta_agendados.css">
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
        </div>
    </nav>

    <div class="container">
        <h1>Alertas Agendados</h1>

        <?php if(isset($_GET['sucesso'])): ?>
        <div class="mensagem-sucesso">
        </div>
        <?php endif; ?>

        <div class="alertas-container">
            <?php if (!empty($alertas_combinados)): ?>
                <?php foreach ($alertas_combinados as $alerta): 
                    $isApi = isset($alerta['origem']) && $alerta['origem'] === 'api';
                ?>
                <div class="alerta-card <?= $isApi ? 'origem-api' : '' ?>">
                    <div class="alerta-icon">⏰</div>
                    <div class="alerta-info">
                        <div class="alerta-header">
                            <span class="alerta-hora">
                                <?= date('H:i', strtotime($alerta['datahora'])) ?>
                            </span>
                        </div>
                        <div class="alerta-compartimento">
                            <?= htmlspecialchars($alerta['descricao_compartimento']) ?>
                        </div>
                        <div class="alerta-medicamento">
                            <?= htmlspecialchars($alerta['comprimidos']) ?>
                        </div>
                        <div class="alerta-footer">
                            <span class="alerta-repeticao">
                                <?= htmlspecialchars($alerta['repeticao']) ?>
                            </span>
                            <?php if(!$isApi): ?>
                            <div class="alerta-acoes">
                                <button onclick="abrirModalEditar(<?= $alerta['idalerta'] ?>)" 
                                        class="btn-editar">
                                    Editar
                                </button>
                                <button onclick="confirmarExclusao(<?= $alerta['idalerta'] ?>)" 
                                        class="btn-excluir">
                                    Excluir
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="api-badge">Sincronizado com a Caixa</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sem-alertas">
                    <p>Nenhum alerta agendado no momento</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este alerta?')) {
            window.location.href = `excluir_alerta.php?id=${id}`;
        }
    }

    window.onclick = function(event) {
        var modal = document.getElementById('modalEditar');
        if (event.target == modal) {
            fecharModal();
        }
    }
</script>

</body>
</html>
