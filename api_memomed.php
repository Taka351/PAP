<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id']) || !isset($_SESSION['name'])) {
    echo json_encode(["mensagem" => "Usuário não autenticado."]);
    exit();
}

include_once 'conexao.php'; // Conexão com o banco de dados

// Verifica se a conexão com o banco foi realizada com sucesso
if (!$mysqli) {
    echo json_encode(["mensagem" => "Erro de conexão com o banco de dados."]);
    exit();
}

// Obtém o ID do usuário
$ref_user = $_SESSION['id'];

// Obtém a hora atual do servidor (para buscar o próximo alerta após a hora atual)
$datahora_atual = date("Y-m-d H:i:s");

// Busca o alerta mais próximo da hora atual
$query = "SELECT modelo, refidcompartimento, datahora FROM alerta 
          WHERE datahora >= ? ORDER BY datahora ASC LIMIT 1";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $datahora_atual);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se existe algum alerta próximo
if ($result->num_rows > 0) {
    $alerta = $result->fetch_assoc();
    echo json_encode($alerta);
} else {
    echo json_encode(["mensagem" => "Nenhum alerta encontrado para o usuário"]);
}
?>
