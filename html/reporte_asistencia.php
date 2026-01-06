<?php
require_once '../php/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia Semanal</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            color: black;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .logo-placeholder {
            width: 100px;
            height: 100px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            color: #666;
        }
        .company-info h1 { margin: 0; font-size: 1.8em; }
        .report-details { text-align: right; }
        
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 12px;
        }
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            background: rgba(255,255,255,0.1);
        }
        .worked { color: #4caf50; font-weight: bold; }
        .absent { color: #f44336; }
        .total-row {
            background: rgba(255,255,255,0.15);
            font-weight: bold;
        }
        @media print {
            .filter-container, .no-print, .btn-group { display: none !important; }
            body { 
                background: white !important; 
                color: black !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                display: block !important; /* Quitar flex que centra verticalmente */
                min-height: auto !important;
            }
            /* Quitar márgenes externos del contenedor principal */
            #main-container { 
                margin: 0 !important; 
                padding: 0 !important; 
                max-width: none !important; 
                width: 100% !important; 
            }
            .report-header { 
                border: 1px solid #ccc; 
                margin: 0 0 10px 0 !important; 
                padding: 10px !important; 
                display: flex !important; 
                background: white !important;
            }
            .logo-placeholder { width: 60px !important; height: 60px !important; }
            .company-info h1 { font-size: 1.4em !important; }
            table { 
                background: white !important; 
                color: black !important; 
                border: 1px solid #000; 
                width: 100%; 
                font-size: 10px; 
                margin-top: 5px; 
                border-collapse: collapse;
            }
            th, td { border: 1px solid #000; color: black; padding: 4px; }
            @page { size: landscape; margin: 1cm !important; }
        }
    </style>
</head>
<body>
    <div id="main-container" style="width: 100%; max-width: 1200px; margin: auto; padding: 20px;">
        
        <div class="report-header">
            <div class="logo-placeholder">LOGO AQUÍ</div>
            <div class="company-info">
                <h1>Nombre de la Empresa</h1>
                <p>Reporte Semanal de Asistencia</p>
            </div>
            <div class="report-details" id="header_detalles" style="display: none;">
                <p><strong>Período:</strong> <span id="rango_fecha"></span></p>
                <p><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
        </div>

        <div class="filter-container">
            <div class="filter-item">
                <label style="color: white; display: block; margin-bottom: 5px;">Semana (Lunes):</label>
                <input type="date" id="filtro_fecha" onchange="cargarReporte()">
            </div>
            <div class="filter-item">
                <label style="color: white; display: block; margin-bottom: 5px;">Filtrar por Cédula:</label>
                <input type="text" id="filtro_cedula" placeholder="Opcional" onkeyup="cargarReporte()">
            </div>
            <div class="filter-item" style="flex: 2; min-width: 300px;">
                <label style="visibility: hidden;">Botones:</label> <!-- Placeholder para alinear flex -->
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button class="btn-primary" style="width: auto; height: 45px; padding: 0 20px;" onclick="window.print()">Imprimir</button>
                    <button class="btn-clear" style="width: auto; height: 45px; padding: 0 20px;" onclick="limpiarReporte()">Limpiar</button>
                    <button class="btn-back" style="width: auto; height: 45px; padding: 0 20px;" onclick="location.href='menu.php'">Menú</button>
                    <button class="btn-delete" style="width: auto; height: 45px; padding: 0 20px;" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table id="tablaReporte">
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <th>Cédula</th>
                        <th>L</th>
                        <th>M</th>
                        <th>M</th>
                        <th>J</th>
                        <th>V</th>
                        <th>S</th>
                        <th>D</th>
                        <th>Días Trab.</th>
                        <th>Val. Día ($)</th>
                        <th>H. Extras</th>
                        <th>Val. H.E ($)</th>
                        <th>Total Semanal ($)</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyReporte">
                    <!-- Datos cargados via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function cargarReporte() {
            const fecha = document.getElementById('filtro_fecha').value;
            const cedula = document.getElementById('filtro_cedula').value;

            if (fecha) {
                const dateStart = new Date(fecha + 'T00:00:00');
                const dateEnd = new Date(dateStart);
                dateEnd.setDate(dateStart.getDate() + 6);
                
                document.getElementById('header_detalles').style.display = 'block';
                document.getElementById('rango_fecha').innerText = 
                    `${dateStart.toLocaleDateString('es-ES')} hasta ${dateEnd.toLocaleDateString('es-ES')}`;
            } else {
                document.getElementById('header_detalles').style.display = 'none';
            }

            fetch(`../php/asistencia_logica.php?action=buscar_reporte&fecha_inicio=${fecha}&cedula=${cedula}`)
                .then(r => r.json())
                .then(res => {
                    const tbody = document.getElementById('tbodyReporte');
                    tbody.innerHTML = '';

                    if (res.success && res.data.length > 0) {
                        let granTotal = 0;
                        res.data.forEach(row => {
                            const dias = [row.lunes, row.martes, row.miercoles, row.jueves, row.viernes, row.sabado, row.domingo];
                            const cantDias = dias.filter(d => d).length;
                            const valDias = cantDias * parseFloat(row.salario_diario || 0);
                            const valExtras = parseFloat(row.horas_extras || 0) * parseFloat(row.valor_hora_extra || 0);
                            const total = valDias + valExtras;
                            granTotal += total;

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.nombres} ${row.apellidos}</td>
                                <td>${row.cedula_trabajador}</td>
                                ${dias.map(d => `<td class="${d ? 'worked' : 'absent'}">${d ? '✔' : '✘'}</td>`).join('')}
                                <td>${cantDias}</td>
                                <td>${parseFloat(row.salario_diario).toFixed(2)}</td>
                                <td>${row.horas_extras}</td>
                                <td>${parseFloat(row.valor_hora_extra).toFixed(2)}</td>
                                <td><strong>${total.toFixed(2)}</strong></td>
                                <td style="font-size: 0.85em;">${row.observaciones || ''}</td>
                            `;
                            tbody.appendChild(tr);
                        });

                        const trTotal = document.createElement('tr');
                        trTotal.className = 'total-row';
                        trTotal.innerHTML = `
                            <td colspan="13" style="text-align: right;">GRAN TOTAL:</td>
                            <td colspan="2">$ ${granTotal.toFixed(2)}</td>
                        `;
                        tbody.appendChild(trTotal);

                    } else {
                        tbody.innerHTML = '<tr><td colspan="14" style="text-align:center;">No hay datos para esta selección</td></tr>';
                    }
                });
        }

        // Cargar reporte vacío al inicio (opcional: poner la fecha de este lunes)
        window.onload = () => {
             // Podríamos preseleccionar el lunes actual here.
        };

        function limpiarReporte() {
            document.getElementById('filtro_fecha').value = '';
            document.getElementById('filtro_cedula').value = '';
            document.getElementById('header_detalles').style.display = 'none';
            document.getElementById('tbodyReporte').innerHTML = '';
        }
    </script>
</body>
</html>
