<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'guardar') {
    $cedula = $_POST['cedula'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $lunes = isset($_POST['lunes']) ? 'true' : 'false';
    $martes = isset($_POST['martes']) ? 'true' : 'false';
    $miercoles = isset($_POST['miercoles']) ? 'true' : 'false';
    $jueves = isset($_POST['jueves']) ? 'true' : 'false';
    $viernes = isset($_POST['viernes']) ? 'true' : 'false';
    $sabado = isset($_POST['sabado']) ? 'true' : 'false';
    $domingo = isset($_POST['domingo']) ? 'true' : 'false';
    $horas_extras = intval($_POST['horas_extras'] ?? 0);
    $observaciones = $_POST['observaciones'] ?? '';

    if (empty($cedula) || empty($fecha_inicio)) {
        echo json_encode(['success' => false, 'message' => 'Cédula y fecha de inicio son requeridas']);
        exit();
    }

    try {
        $sql = "INSERT INTO public.asistencia_semanal 
                (cedula_trabajador, fecha_inicio, fecha_fin, lunes, martes, miercoles, jueves, viernes, sabado, domingo, horas_extras, observaciones)
                VALUES (?, ?, ?, $lunes, $martes, $miercoles, $jueves, $viernes, $sabado, $domingo, ?, ?)
                ON CONFLICT (cedula_trabajador, fecha_inicio) 
                DO UPDATE SET 
                    fecha_fin = EXCLUDED.fecha_fin,
                    lunes = EXCLUDED.lunes,
                    martes = EXCLUDED.martes,
                    miercoles = EXCLUDED.miercoles,
                    jueves = EXCLUDED.jueves,
                    viernes = EXCLUDED.viernes,
                    sabado = EXCLUDED.sabado,
                    domingo = EXCLUDED.domingo,
                    horas_extras = EXCLUDED.horas_extras,
                    observaciones = EXCLUDED.observaciones";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$cedula, $fecha_inicio, $fecha_fin, $horas_extras, $observaciones]);

        // Actualizar salario en el registro maestro
        if (isset($_POST['salario_diario']) && isset($_POST['valor_hora_extra'])) {
            $stmtSalario = $conexion->prepare("UPDATE public.registro SET salario_diario = ?, valor_hora_extra = ? WHERE cedula = ?");
            $stmtSalario->execute([$_POST['salario_diario'], $_POST['valor_hora_extra'], $cedula]);
        }

        echo json_encode(['success' => true, 'message' => 'Asistencia y valores actualizados correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} elseif ($action === 'buscar_reporte') {
    $fecha_inicio = $_GET['fecha_inicio'] ?? '';
    $cedula = $_GET['cedula'] ?? '';

    try {
        $sql = "SELECT a.*, r.nombres, r.apellidos, r.salario_diario, r.valor_hora_extra 
                FROM public.asistencia_semanal a
                JOIN public.registro r ON a.cedula_trabajador = r.cedula
                WHERE 1=1";
        $params = [];

        if (!empty($fecha_inicio)) {
            $sql .= " AND a.fecha_inicio = ?";
            $params[] = $fecha_inicio;
        }

        if (!empty($cedula)) {
            $sql .= " AND a.cedula_trabajador = ?";
            $params[] = $cedula;
        }

        $sql .= " ORDER BY a.fecha_inicio DESC, r.apellidos ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $resultados]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} elseif ($action === 'obtener_asistencia') {
    $cedula = $_GET['cedula'] ?? '';
    $fecha_inicio = $_GET['fecha_inicio'] ?? '';

    try {
        $stmt = $conexion->prepare("SELECT * FROM public.asistencia_semanal WHERE cedula_trabajador = ? AND fecha_inicio = ?");
        $stmt->execute([$cedula, $fecha_inicio]);
        $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $asistencia]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
