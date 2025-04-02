<?php
session_start();
include_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alerta_id = $_POST['alerta_id'];
    $datahora = $_POST['datahora'];
    $repeticao = $_POST['repeticao'];
    
    $query = "UPDATE alerta SET datahora = ?, repeticao = ? WHERE idalerta = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssi", $datahora, $repeticao, $alerta_id);
    if ($stmt->execute()) {
        header("Location: alertas_agendados.php?sucesso=1");
    } else {
        header("Location: alertas_agendados.php?erro=1");
    }
    exit();
}
?>
