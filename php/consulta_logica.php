<?php
// consulta_logica.php

// Incluir el archivo de conexión PDO. Ahora expone $conexion.
include('conexion.php'); 

$resultados_html = "";
$trabajador_nombre = "";
$parametros = [];
$query_sql = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['cedula']) || isset($_POST['id_recibo']))) {
    
    $base_query = "
        SELECT 
            n.*, r.nombres, r.apellidos, r.cargo
        FROM 
            nomina n 
        JOIN 
            registro r ON n.cedula_trabajador = r.cedula 
    ";
    
    if (!empty($_POST['cedula'])) {
        // Usa marcadores de posición con nombre (:cedula)
        $query_sql = $base_query . " WHERE n.cedula_trabajador = :cedula ORDER BY n.fecha_registro DESC";
        $parametros = [':cedula' => $_POST['cedula']];
    } elseif (!empty($_POST['id_recibo'])) {
        // Usa marcadores de posición con nombre (:id_recibo)
        $query_sql = $base_query . " WHERE n.id_recibo = :id_recibo ORDER BY n.fecha_registro DESC";
        $parametros = [':id_recibo' => (int)$_POST['id_recibo']];
    }

    if ($query_sql) {
        try {
            // Usa PDO: prepare(), execute(), fetchAll()
            $stmt = $conexion->prepare($query_sql); // Prepara la consulta usando $conexion
            $stmt->execute($parametros); // Ejecuta con los parámetros seguros
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtiene todos los resultados

            if (count($resultados) > 0) {
                // Capturar nombre del primer resultado
                $firstRow = $resultados[0]; // Accede al primer elemento del array
                $trabajador_nombre = $firstRow['nombres'] . " " . $firstRow['apellidos'] . " (" . $firstRow['cargo'] . ")";
                
                foreach ($resultados as $row) {
                    $fecha_formateada = date('d/m/Y', strtotime($row['fecha_registro']));
                    $usd_format = number_format($row['total_usd'], 2);
                    $bs_format = number_format($row['total_bs'], 2);
                    
                    $datos_json = json_encode($row);

                    // Escapar los datos JSON para que sean seguros dentro del atributo HTML onclick
                    $datos_json_seguro = htmlspecialchars($datos_json, ENT_QUOTES, 'UTF-8');

                    $resultados_html .= "
                        <tr>
                            <td>" . htmlspecialchars($row['id_recibo']) . "</td>
                            <td>{$fecha_formateada}</td>
                            <td>{$usd_format} $</td>
                            <td>{$bs_format} Bs</td>
                            <td class='no-print'>
                                <button onclick='prepararImpresion({$datos_json_seguro})' class='btn btn-print'>Imprimir<span class='desktop-only'> Recibo</span></button>
                            </td>
                        </tr>
                    ";
                }
            } else {
                $resultados_html = "<tr><td colspan='5'>No se encontraron recibos para los criterios de búsqueda.</td></tr>";
            }

        } catch (PDOException $e) {
            $resultados_html = "<tr><td colspan='5' style='color:red;'>Error en la consulta: " . $e->getMessage() . "</td></tr>";
        }
    }
}
?>
