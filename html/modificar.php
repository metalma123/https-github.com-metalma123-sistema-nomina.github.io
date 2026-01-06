<?php
require_once '../php/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Trabajador</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <div class="container">
        <h2>Modificar Trabajador</h2>

        <!-- Buscador -->
        <div class="search-box">
            <input type="text" id="buscar_cedula" placeholder="Ingrese Cédula...">
            <button type="button" class="btn-search" onclick="buscarTrabajador()">Buscar trabajador</button>
        </div>

        <!-- Formulario de Edición -->
        <form id="formModificar" action="../php/buscar.php" method="POST">
            <input type="hidden" id="id_registro" name="id_registro">

            <div class="form-group">
                <label>Cédula (No editable):</label>
                <input type="text" id="cedula" name="cedula" readonly style="background: #e9ecef;">
            </div>
            <div class="form-group">
                <label>Nombres:</label>
                <input type="text" id="nombres" name="nombres" required>
            </div>
            <div class="form-group">
                <label>Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            <div class="form-group">
                <label>Cargo:</label>
                <input type="text" id="cargo" name="cargo">
            </div>
            <div class="form-group">
                <label>Fecha de Ingreso:</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" id="telefono" name="telefono">
            </div>
            <div class="form-group">
                <label>Dirección:</label>
                <textarea id="direccion" name="direccion"></textarea>
            </div>
            <div class="form-group">
                <label>Salario Diario (USD):</label>
                <input type="number" step="0.01" id="salario_diario" name="salario_diario">
            </div>
            <div class="form-group">
                <label>Valor Hora Extra (USD):</label>
                <input type="number" step="0.01" id="valor_hora_extra" name="valor_hora_extra">
            </div>

            <div class="btns">
                <button type="submit" class="btn-save btn-modificar">Actualizar Datos</button>
                <button type="button" class="btn-clear" onclick="limpiarCampos()">Limpiar</button>
                <button type="button" class="btn-back" onclick="location.href='menu.php'">Menú</button>
                <button type="button" class="btn-delete" style="margin-top:10px;" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
            </div>
        </form>
    </div>

    <script>
        function buscarTrabajador() {
            const cedula = document.getElementById('buscar_cedula').value;
            if (!cedula) return alert("Ingrese una cédula");

            fetch(`../php/buscar.php?cedula=${cedula}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        limpiarCampos();
                    } else {
                        document.getElementById('id_registro').value = data.id_registro;
                        document.getElementById('cedula').value = data.cedula;
                        document.getElementById('nombres').value = data.nombres;
                        document.getElementById('apellidos').value = data.apellidos;
                        document.getElementById('cargo').value = data.cargo;
                        document.getElementById('fecha_ingreso').value = data.fecha_ingreso;
                        document.getElementById('telefono').value = data.telefono;
                        document.getElementById('direccion').value = data.direccion;
                        document.getElementById('salario_diario').value = data.salario_diario;
                        document.getElementById('valor_hora_extra').value = data.valor_hora_extra;
                    }
                });
        }

        function limpiarCampos() {
            document.getElementById('formModificar').reset();
            document.getElementById('id_registro').value = '';
            document.getElementById('buscar_cedula').value = '';
        }
    </script>

</body>

</html>