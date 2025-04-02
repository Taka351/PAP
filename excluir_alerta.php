<?php
session_start();
include_once 'conexao.php';

if (isset($_GET['id'])) {
    $alerta_id = $_GET['id'];
    
    $query = "DELETE FROM alerta WHERE idalerta = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $alerta_id);
    
    if ($stmt->execute()) {
        header("Location: alertas_agendados.php?sucesso=2");
    } else {
        header("Location: alertas_agendados.php?erro=2");
    }
    exit();
}
?>
