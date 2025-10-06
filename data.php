<?php
// data.php (API para métricas)

// Solo permitimos peticiones GET y salimos si no es así
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit;
}

// Configurar encabezados para devolver JSON
header('Content-Type: application/json');
include('db_connection.php');

// LÓGICA DE OBTENER DATOS
// 1. Total de Pacientes
$pacientes_result = $conn->query("SELECT COUNT(id) AS total FROM pacientes");
$total_pacientes = $pacientes_result ? $pacientes_result->fetch_assoc()['total'] : 0;

// 2. Citas Pendientes
$pendientes_result = $conn->query("SELECT COUNT(id) AS total FROM citas WHERE estado = 'pendiente'");
$citas_pendientes = $pendientes_result ? $pendientes_result->fetch_assoc()['total'] : 0;

// 3. Citas por Estado (para el gráfico)
$citas_estado_result = $conn->query("SELECT estado, COUNT(id) as count FROM citas GROUP BY estado");
$citas_data = $citas_estado_result ? $citas_estado_result->fetch_all(MYSQLI_ASSOC) : [];

// Estructurar la respuesta
$response = [
    'status' => 'success',
    'metrics' => [
        'total_pacientes' => $total_pacientes,
        'citas_pendientes' => $citas_pendientes,
    ],
    'chart_data' => $citas_data
];

echo json_encode($response);
$conn->close();
?>