<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "memomed";

// Conectar ao MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["erro" => "Falha na conexão"]));
}

// Buscar os alertas da tabela 'alerta'
$sql = "SELECT idalerta, datahora, refidcompartimento, repeticao, modelo FROM alerta ORDER BY datahora ASC";
$result = $conn->query($sql);

$alertas = [];
while ($row = $result->fetch_assoc()) {
    $alertas[] = $row;
}

// Retornar os dados em JSON
echo json_encode($alertas);
$conn->close();
?>