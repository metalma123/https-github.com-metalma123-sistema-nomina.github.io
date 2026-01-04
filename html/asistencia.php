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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia Semanal</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .grid-asistencia {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .dia-item {
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 8px;
        }
        .dia-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="checkbox"] {
            width: 25px;
            height: 25px;
            cursor: pointer;
        }
        .search-container {
            margin-bottom: 30px;
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .search-title {
            margin-top: 0;
            color: #000000 !important; /* Negro puro */
            font-size: 1.25em;
            margin-bottom: 15px;
            font-weight: bold;
        }
        #buscar_cedula {
            font-size: 1.2em;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #4a90e2;
            background: rgba(255,255,255,0.9);
            color: #333;
            width: 100%;
            max-width: 400px;
        }
        .btn-large {
            padding: 12px 25px;
            font-size: 1.1em;
            cursor: pointer;
        }
        .salary-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            background: rgba(74, 144, 226, 0.1);
            padding: 15px;
            border-radius: 8px;
        }
        
    </style>
</head>
<body>
    <div class="form-container" style="max-width: 900px;">
        <h2>Control de Asistencia Semanal</h2>
        
        <div class="search-container">
            <h3 class="search-title">Buscar Trabajador por Cédula</h3>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" id="buscar_cedula" placeholder="Ej: 12345678">
                <button type="button" class="btn-primary" style="padding: 12px 20px;" onclick="buscarTrabajador()">Buscar</button>
                <button type="button" class="btn-clear" style="padding: 12px 20px;" onclick="limpiarFormulario()">Limpiar</button>
                <button type="button" class="btn-back" style="padding: 12px 20px;" onclick="location.href='menu.php'">Menú</button>
            </div>
        </div>

        <div id="info_trabajador" style="margin-bottom: 20px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 8px; display: none; border-left: 5px solid #4caf50;">
            <p style="font-size: 1.1em;"><strong>Trabajador:</strong> <span id="nombre_completo"></span></p>
            <p><strong>Cédula:</strong> <span id="cedula_display"></span></p>
        </div>

        <form id="formAsistencia" style="display: none;">
            <input type="hidden" name="cedula" id="form_cedula">
            
            <div class="form-group">
                <label for="fecha_inicio">Lunes de la Semana:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required onchange="actualizarFechas()">
                <small style="color: #ccc;">Seleccione el Lunes que inicia la semana a reportar</small>
            </div>

            <div class="salary-fields">
                <div class="form-group">
                    <label>Valor del Día ($):</label>
                    <input type="number" step="0.01" id="salario_diario" name="salario_diario" required>
                </div>
                <div class="form-group">
                    <label>Valor Hora Extra ($):</label>
                    <input type="number" step="0.01" id="valor_hora_extra" name="valor_hora_extra" required>
                </div>
            </div>

            <div class="grid-asistencia">
                <div class="dia-item">
                    <label>Lun</label>
                    <input type="checkbox" name="lunes" id="check_lunes">
                    <div id="label_lunes" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Mar</label>
                    <input type="checkbox" name="martes" id="check_martes">
                    <div id="label_martes" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Mié</label>
                    <input type="checkbox" name="miercoles" id="check_miercoles">
                    <div id="label_miercoles" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Jue</label>
                    <input type="checkbox" name="jueves" id="check_jueves">
                    <div id="label_jueves" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Vie</label>
                    <input type="checkbox" name="viernes" id="check_viernes">
                    <div id="label_viernes" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Sáb</label>
                    <input type="checkbox" name="sabado" id="check_sabado">
                    <div id="label_sabado" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
                <div class="dia-item">
                    <label>Dom</label>
                    <input type="checkbox" name="domingo" id="check_domingo">
                    <div id="label_domingo" style="font-size: 0.8em; margin-top: 5px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="horas_extras">Horas Extras Totales de la Semana:</label>
                <input type="number" id="horas_extras" name="horas_extras" value="0" min="0">
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="2"></textarea>
            </div>

            <input type="hidden" name="fecha_fin" id="fecha_fin">

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-primary">Guardar Asistencia</button>
                <button type="button" class="btn-back" onclick="location.href='menu.php'">Menú</button>
            </div>
        </form>
    </div>

    <script>
        function buscarTrabajador() {
            const cedula = document.getElementById('buscar_cedula').value;
            if (!cedula) return alert('Ingrese una cédula');

            fetch(`../php/buscar_trabajador.php?cedula=${cedula}`)
                .then(r => r.json())
                .then(res => {
                    if (res && !res.error) {
                        document.getElementById('info_trabajador').style.display = 'block';
                        document.getElementById('formAsistencia').style.display = 'block';
                        document.getElementById('nombre_completo').innerText = `${res.nombres} ${res.apellidos}`;
                        document.getElementById('cedula_display').innerText = res.cedula;
                        document.getElementById('form_cedula').value = res.cedula;
                        document.getElementById('salario_diario').value = res.salario_diario;
                        document.getElementById('valor_hora_extra').value = res.valor_hora_extra;
                        cargarExistente();
                    } else {
                        alert('Trabajador no encontrado');
                        document.getElementById('info_trabajador').style.display = 'none';
                        document.getElementById('formAsistencia').style.display = 'none';
                    }
                });
        }

        function actualizarFechas() {
            const startInput = document.getElementById('fecha_inicio');
            if (!startInput.value) return;

            const date = new Date(startInput.value + 'T00:00:00');
            const day = date.getDay(); // 0=Sun, 1=Mon...
            
            if (day !== 1) {
                alert('Por favor, seleccione un Lunes');
                startInput.value = '';
                return;
            }

            const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            for (let i = 0; i < 7; i++) {
                const d = new Date(date);
                d.setDate(date.getDate() + i);
                document.getElementById('label_' + dias[i]).innerText = d.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit'});
                if (i === 6) {
                    document.getElementById('fecha_fin').value = d.toISOString().split('T')[0];
                }
            }
            cargarExistente();
        }

        function cargarExistente() {
            const cedula = document.getElementById('form_cedula').value;
            const fecha = document.getElementById('fecha_inicio').value;
            if (!cedula || !fecha) return;

            fetch(`../php/asistencia_logica.php?action=obtener_asistencia&cedula=${cedula}&fecha_inicio=${fecha}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.data) {
                        document.getElementById('check_lunes').checked = res.data.lunes;
                        document.getElementById('check_martes').checked = res.data.martes;
                        document.getElementById('check_miercoles').checked = res.data.miercoles;
                        document.getElementById('check_jueves').checked = res.data.jueves;
                        document.getElementById('check_viernes').checked = res.data.viernes;
                        document.getElementById('check_sabado').checked = res.data.sabado;
                        document.getElementById('check_domingo').checked = res.data.domingo;
                        document.getElementById('horas_extras').value = res.data.horas_extras;
                        document.getElementById('observaciones').value = res.data.observaciones;
                    } else {
                        // Limpiar campos de asistencia pero mantener cedula y fecha
                        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(c => c.checked = false);
                        document.getElementById('horas_extras').value = 0;
                        document.getElementById('observaciones').value = '';
                    }
                });
        }

        document.getElementById('formAsistencia').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../php/asistencia_logica.php?action=guardar', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                alert(res.message);
            });
        };

        function limpiarFormulario() {
            document.getElementById('formAsistencia').reset();
            document.getElementById('formAsistencia').style.display = 'none';
            document.getElementById('info_trabajador').style.display = 'none';
            document.getElementById('buscar_cedula').value = '';
            const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            dias.forEach(d => {
                const el = document.getElementById('label_' + d);
                if (el) el.innerText = '';
            });
        }
    </script>
</body>
</html>
