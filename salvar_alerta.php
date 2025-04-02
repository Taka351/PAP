<?php
session_start();
include_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Primeiro, obter o ID da caixa do usuário atual
    $ref_user = $_SESSION['id'];
    $query_caixa = "SELECT idcaixa, modelo FROM caixa WHERE ref_user = ?";
    $stmt_caixa = $mysqli->prepare($query_caixa);
    $stmt_caixa->bind_param("i", $ref_user);
    $stmt_caixa->execute();
    $result_caixa = $stmt_caixa->get_result()->fetch_assoc();
    $caixa_id = $result_caixa['idcaixa'];
    $modelo_caixa = $result_caixa['modelo'];
    
    // Criar o compartimento se não existir
    $compartimento_numero = $_POST['compartimento_id'];
    $descricao = "Compartimento " . $compartimento_numero;
    $comprimidos = $_POST['comprimidos']; // Novo campo para nome do comprimido
    
    // Verificar se o compartimento já existe
    $query_check = "SELECT idcompartimento FROM compartimento 
                    WHERE ref_idcaixa = ? AND descricao_compartimento = ?";
    $stmt_check = $mysqli->prepare($query_check);
    $stmt_check->bind_param("is", $caixa_id, $descricao);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if ($result->num_rows === 0) {
        // Criar novo compartimento com nome do comprimido
        $query_comp = "INSERT INTO compartimento (descricao_compartimento, comprimidos, ref_idcaixa) 
                    VALUES (?, ?, ?)";
        $stmt_comp = $mysqli->prepare($query_comp);
        $stmt_comp->bind_param("ssi", $descricao, $comprimidos, $caixa_id);
        $stmt_comp->execute();
        $compartimento_id = $mysqli->insert_id;
    } else {
        $compartimento_id = $result->fetch_assoc()['idcompartimento'];
        // Atualizar o nome do comprimido no compartimento existente
        $query_update = "UPDATE compartimento SET comprimidos = ? WHERE idcompartimento = ?";
        $stmt_update = $mysqli->prepare($query_update);
        $stmt_update->bind_param("si", $comprimidos, $compartimento_id);
        $stmt_update->execute();
    }
    
    // Inserir o alerta usando o ID correto do compartimento
    $datahora = $_POST['datahora'];
    $repeticao = $_POST['repeticao'];
    
    $query_alerta = "INSERT INTO alerta (datahora, refidcompartimento, repeticao) 
                    VALUES (?, ?, ?)";
    $stmt_alerta = $mysqli->prepare($query_alerta);
    $stmt_alerta->bind_param("sis", $datahora, $compartimento_id, $repeticao);
    
    if ($stmt_alerta->execute()) {
        // API Call - Usando o modelo da caixa (QR_002) em vez do ID
        $apiKey = "XRP01233TT8912K";
        $caixaId = $modelo_caixa; // Usa o modelo (QR_002) em vez do ID
        $compartimentoValue = $compartimento_numero;

        $apiUrl = "https://bluecloud.pt/esag_apoioalunos/api_caixas/InserirCaixaCompartimento.php?ApiKey={$apiKey}&Caixa={$caixaId}&Compartimento={$compartimentoValue}";

        // Make API call
        $result = @file_get_contents($apiUrl);
        
        // Redirect to alertas_agendados.php
        header("Location: alertas_agendados.php?sucesso=1");
    } else {
        header("Location: caixa.php?erro=1");
    }
    exit();
}
?>
