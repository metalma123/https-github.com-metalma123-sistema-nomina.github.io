<?php


// auto recivo 
include '../php/conexion.php';
try {
    $stmt = $conexion->query("SELECT MAX(id_recibo) as max_id FROM nomina");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nro_recibo = ($row && $row['max_id']) ? $row['max_id'] + 1 : 1;
} catch (PDOException $e) {
    $nro_recibo = "ERROR";
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Contamos 12 columnas y 12 signos de interrogación
        $sql = "INSERT INTO nomina (
            cedula_trabajador,    -- 1
            dias_trabajados,      -- 2
            valor_dia_usd,        -- 3
            horas_extras,         -- 4
            valor_hora_extra_usd, -- 5
            tasa_cambio_bs,       -- 6 (Aquí estaba el error)
            bonus_usd,            -- 7
            seguro_social_usd,    -- 8
            prestamos_usd,        -- 9
            total_usd,            -- 10
            total_bs,             -- 11
            observaciones         -- 12
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql);
        

        // El orden en el execute DEBE ser idéntico al de arriba
        $stmt->execute([
            $_POST['cedula'],       // 1
            $_POST['dias'],         // 2
            $_POST['v_dia'],        // 3
            $_POST['h_extras'],     // 4
            $_POST['v_h_extra'],    // 5
            $_POST['tasa'],         // 6 <--- Verifica que en el HTML el input sea name="tasa"
            $_POST['bonus'],        // 7
            $_POST['seguro'],       // 8
            $_POST['prestamos'],    // 9
            $_POST['total_usd'],    // 10
            $_POST['total_bs'],     // 11
            $_POST['observaciones'] // 12
        ]);

        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Guardado exitosamente', 'id_recibo' => $nro_recibo]);
            exit;
        } else {
            echo "<script>alert('Guardado exitosamente'); window.location='../html/nomina.html';</script>";
        }

    } catch (PDOException $e) {
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        } else {
            echo "Error crítico: " . $e->getMessage();
        }
    }
}
?>