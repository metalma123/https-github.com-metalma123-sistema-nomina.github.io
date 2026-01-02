<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="css/estilos.css" />
</head>

<body>
    <div style="width: 100%; max-width: 800px; margin: auto;">
        <h2 style="color: white; margin-top: 20px;">Menú Principal</h2>
        <div class="tarjetas">
            <div class="tarjeta">
                <h3>Calcular Nómina</h3>
                <button onclick="location.href='calculo_nomina.php'" class="btn-primary">Calcular</button>
            </div>
            <div class="tarjeta">
                <h3>Registrar Trabajador</h3>
                <button onclick="location.href='registro.php'" class="btn-primary">Registrar</button>
            </div>
            <div class="tarjeta">
                <h3>Modificar Trabajador</h3>
                <button onclick="location.href='modificar.php'" class="btn-modificar">Modificar</button>
            </div>
            <div class="tarjeta">
                <h3>Eliminar Trabajador</h3>
                <button onclick="location.href='eliminar.php'" class="btn-delete">Eliminar</button>
            </div>
            <div class="tarjeta">
                <h3>Consulta Trabajadores</h3>
                <button onclick="location.href='consulta.php'" class="btn-search">Consulta</button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button onclick="location.href='../php/logout.php'" class="btn-back" style="max-width: 200px; margin: auto;">Cerrar Sesión</button>
        </div>
    </div>
</body>

</html>