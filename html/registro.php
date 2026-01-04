<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="form-container">
        <h2>Registro de Trabajador</h2>
        <form action="../php/registro.php" method="POST">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" placeholder="Ej: 12345678" required>
            </div>
            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <input type="text" id="cargo" name="cargo">
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" name="direccion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="salario_diario">Salario Diario (USD):</label>
                <input type="number" step="0.01" id="salario_diario" name="salario_diario" value="0.00" required>
            </div>
            <div class="form-group">
                <label for="valor_hora_extra">Valor Hora Extra (USD):</label>
                <input type="number" step="0.01" id="valor_hora_extra" name="valor_hora_extra" value="0.00" required>
            </div>
            <div class="form-group">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-primary">Guardar Registro</button>
            <button type="button" class="btn-back" onclick="location.href='menu.php'">Menú</button>
            <button type="button" class="btn-delete" style="margin-top:10px;" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
        </form>
    </div>
</body>

</html>