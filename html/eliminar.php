<?php
require_once '../php/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Trabajador</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="container">
        <h2>Eliminar Registro de Personal</h2>

        <!-- Buscador -->
        <div class="search-box">
            <input type="text" id="buscar_cedula" placeholder="Ingrese Cédula para buscar...">
            <button type="button" class="btn-search" onclick="buscarParaEliminar()">Accionar Busqueda</button>
        </div>

        <!-- Formulario de Confirmación (Todo readonly para seguridad) -->
        <form id="formEliminar" action="../php/phpdelete.php" method="POST" onsubmit="return confirmarEliminacion()">
            <input type="hidden" id="id_registro" name="id_registro">

            <div class="form-group">
                <label>Nombre Completo:</label>
                <input type="text" id="nombre_completo" readonly>
            </div>
            <div class="form-group">
                <label>Cédula:</label>
                <input type="text" id="cedula" name="cedula" readonly>
            </div>
            <div class="form-group">
                <label>Cargo Actual:</label>
                <input type="text" id="cargo" readonly>
            </div>

            <div class="btns">
                <button type="submit" id="btnBorrar" class="btn-delete" disabled>Eliminar Definitivamente</button>
                <button type="button" class="btn-clear" onclick="limpiar()">Limpiar</button>
                <button type="button" class="btn-back" onclick="location.href='menu.php'">Volver al Menú</button>
                <button type="button" class="btn-delete" style="margin-top:10px; background: #dc3545;" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
            </div>
        </form>
    </div>

    <script>
        function buscarParaEliminar() {
            const cedula = document.getElementById('buscar_cedula').value;
            if (!cedula) return alert("Por favor, ingrese una cédula.");

            // Reutilizamos la lógica de búsqueda (puedes usar el buscar_trabajador.php que creamos antes)
            fetch(`../php/buscar_trabajador.php?cedula=${cedula}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        limpiar();
                    } else {
                        document.getElementById('id_registro').value = data.id_registro;
                        document.getElementById('cedula').value = data.cedula;
                        document.getElementById('nombre_completo').value = data.nombres + " " + data.apellidos;
                        document.getElementById('cargo').value = data.cargo;
                        document.getElementById('btnBorrar').disabled = false; // Habilitar botón de borrar
                    }
                });
        }

        function confirmarEliminacion() {
            return confirm("¿ESTÁ SEGURO? Esta acción borrará al trabajador de forma permanente en 2025 y no se puede deshacer.");
        }

        function limpiar() {
            document.getElementById('formEliminar').reset();
            document.getElementById('buscar_cedula').value = '';
            document.getElementById('btnBorrar').disabled = true;
        }
    </script>

</body>

</html>