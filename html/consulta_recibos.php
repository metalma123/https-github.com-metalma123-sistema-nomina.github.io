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
    <title>Consulta de Recibos - Sistema de Nómina</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos de Impresión */
        #printable-receipt { display: none; }
        @media print {
            body * { visibility: hidden; }
            #printable-receipt, #printable-receipt * { visibility: visible; }
            #printable-receipt { 
                display: block !important; 
                position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 20px; background: white;
            }
            .no-print, .container { display: none !important; }
        }

        .seccion-consulta { margin-bottom: 20px; padding: 20px; background: rgba(255,255,255,0.8); border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .grid-consultas { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .tabla-resultados { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; }
        .tabla-resultados th, .tabla-resultados td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .tabla-resultados th { background: #007bff; color: white; }
        .tabla-resultados tr:hover { background: #f8f9fa; cursor: pointer; }
        
        .btn-accion { padding: 8px 15px; border-radius: 5px; cursor: pointer; border: none; font-weight: bold; }
        .btn-print-mini { background: #28a745; color: white; }

        /* Estilos Responsivos */
        @media screen and (max-width: 768px) {
            .grid-consultas { grid-template-columns: 1fr; gap: 15px; }
            .container { padding: 10px; }
            h1 { font-size: 1.5rem; text-align: center; }
            .btn-group { display: flex; flex-direction: column; gap: 10px; }
            .btn-group button { width: 100%; margin: 0; }
            .tabla-resultados { font-size: 14px; }
            .tabla-resultados th, .tabla-resultados td { padding: 8px; }
            input[type="text"], input[type="number"] { font-size: 16px; padding: 12px !important; } /* Previene zoom en iOS y mejora tacto */
            .btn-calc { padding: 12px !important; }
        }
    </style>
</head>
<body>
    <div class="container no-print">
        <h1>Consulta de Recibos de Pago</h1>
        
        <div class="seccion-consulta">
            <div class="grid-consultas">
                <div>
                    <label>Buscar por Cédula:</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" id="busqueda_cedula" placeholder="Ingrese cédula...">
                        <button type="button" class="btn-calc" onclick="buscarPorCedula()">Buscar</button>
                    </div>
                </div>
                <div>
                    <label>Buscar por N° Recibo:</label>
                    <div style="display:flex; gap:10px;">
                        <input type="number" id="busqueda_recibo" placeholder="N° de recibo...">
                        <button type="button" class="btn-calc" onclick="buscarPorRecibo()">Consultar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="resultados_container" style="display:none;">
            <h3>Resultados de la Búsqueda</h3>
            <div style="overflow-x: auto;">
                <table class="tabla-resultados">
                    <thead>
                        <tr>
                            <th>N° Recibo</th>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Total (USD)</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="lista_recibos">
                        <!-- Filas cargadas dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 30px;">
            <button type="button" class="btn-clear" onclick="limpiarCampos()">Limpiar Campos</button>
            <button type="button" class="btn-back" onclick="location.href='menu.php'">Volver al Menú</button>
            <button type="button" class="btn-delete" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
        </div>
    </div>

    <!-- Contenedor oculto para la impresión -->
    <div id="printable-receipt"></div>

    <script>
        function buscarPorCedula() {
            const cedula = document.getElementById('busqueda_cedula').value;
            if(!cedula) return alert("Ingrese una cédula");

            fetch(`../php/consultar_recibos_logica.php?cedula=${cedula}`)
            .then(r => r.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                } else if(data.length === 0) {
                    alert("No se encontraron recibos para esta cédula");
                } else {
                    mostrarLista(data);
                }
            });
        }

        function buscarPorRecibo() {
            const id_recibo = document.getElementById('busqueda_recibo').value;
            if(!id_recibo) return alert("Ingrese el número de recibo");

            fetch(`../php/consultar_recibos_logica.php?id_recibo=${id_recibo}`)
            .then(r => r.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                } else {
                    generarReciboHTML(data);
                }
            });
        }

        function mostrarLista(recibos) {
            const container = document.getElementById('resultados_container');
            const tbody = document.getElementById('lista_recibos');
            tbody.innerHTML = '';
            
            recibos.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${String(r.id_recibo).padStart(6, '0')}</td>
                    <td>${r.fecha_registro || 'N/A'}</td>
                    <td>${r.nombres} ${r.apellidos}</td>
                    <td>${r.total_usd} $</td>
                    <td>
                        <button class="btn-accion btn-print-mini" onclick="event.stopPropagation(); obtenerYImprimir(${r.id_recibo})">Imprimir</button>
                    </td>
                `;
                tr.onclick = () => obtenerYImprimir(r.id_recibo);
                tbody.appendChild(tr);
            });
            
            container.style.display = 'block';
        }

        function obtenerYImprimir(id) {
            fetch(`../php/consultar_recibos_logica.php?id_recibo=${id}`)
            .then(r => r.json())
            .then(data => {
                if(data.error) alert(data.error);
                else generarReciboHTML(data);
            });
        }

        function limpiarCampos() {
            document.getElementById('busqueda_cedula').value = '';
            document.getElementById('busqueda_recibo').value = '';
            document.getElementById('resultados_container').style.display = 'none';
        }

        function generarReciboHTML(datos) {
            const printDiv = document.getElementById('printable-receipt');
            const fechaRecibo = datos.fecha_registro ? new Date(datos.fecha_registro).toLocaleString() : 'N/A';

            printDiv.innerHTML = `
                <style>
                    .recibo-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
                    .recibo-table th, .recibo-table td { border: 1px solid #dddddd; padding: 8px; text-align: left; }
                    .recibo-table th { background-color: #f2f2f2; font-weight: bold; }
                    .header-logo { text-align: center; margin-bottom: 20px; }
                    .firma-section { display: flex; justify-content: space-between; margin-top: 50px; }
                    .firma-box { text-align: center; width: 40%; }
                </style>

                <div style="border: 2px solid #000; padding: 30px; width: 700px; margin: auto; font-family: Arial, sans-serif; background: white;">
                    <div class="header-logo">
                        <div style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">LOGO EMPRESA</div>
                        <h2 style="margin: 5px 0;">SISTEMA DE NÓMINA 2026</h2>
                        <h3 style="margin: 5px 0; color: #555;">Copia de Recibo de Pago</h3>
                        <p style="font-size: 12px; margin: 5px 0;">Recibo N°: <span style="color:red; font-size:16px;">${String(datos.id_recibo).padStart(6, '0')}</span></p>
                    </div>

                    <hr style="border: 1px solid #ccc;">

                    <div style="margin-bottom: 15px;">
                        <p><strong>Nombres y Apellidos:</strong> ${datos.nombres} ${datos.apellidos}</p>
                        <p><strong>Cédula de Identidad:</strong> ${datos.cedula_trabajador}</p>
                        <p><strong>Cargo:</strong> ${datos.cargo}</p>
                    </div>

                    <table class="recibo-table">
                        <thead>
                            <tr>
                                <th colspan="2" style="text-align:center; background:#e9ecef;">Detalles del Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Días Trabajados:</td><td>${datos.dias_trabajados}</td></tr>
                            <tr><td>Valor Día:</td><td>${datos.valor_dia_usd} USD</td></tr>
                            <tr><td>Horas Extras:</td><td>${datos.horas_extras}</td></tr>
                            <tr><td>Valor Hora Extra:</td><td>${datos.valor_hora_extra_usd} USD</td></tr>
                        </tbody>
                    </table>

                    <table class="recibo-table">
                        <thead>
                            <tr>
                                <th colspan="2" style="text-align:center; background:#e9ecef;">Bonificaciones y Deducciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td style="color:green;">+ Bono Especial:</td><td>${datos.bonus_usd} USD</td></tr>
                            <tr><td style="color:red;">- Seguro Social:</td><td>${datos.seguro_social_usd} USD</td></tr>
                            <tr><td style="color:red;">- Préstamos / Otros:</td><td>${datos.prestamos_usd} USD</td></tr>
                        </tbody>
                    </table>

                    <hr style="border: 1px solid #ccc; margin: 20px 0;">

                    <div style="text-align: right; font-size: 16px;">
                        <p><strong>Tasa de Cambio:</strong> ${datos.tasa_cambio_bs} Bs/USD</p>
                        <h3 style="margin: 5px 0; color: #007bff;">Total Pagado (USD): ${datos.total_usd} $</h3>
                        <h3 style="margin: 5px 0; color: #28a745;">Total Pagado (Bs): ${datos.total_bs} Bs</h3>
                    </div>

                    <p style="margin-top: 10px; font-style: italic;"><strong>Observaciones:</strong> ${datos.observaciones || 'Sin observaciones'}</p>

                    <div class="firma-section">
                        <div class="firma-box">
                            <br><br>
                            <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                            <p style="margin-top: 5px;"><strong>Firma del Trabajador</strong></p>
                            <small>C.I: ${datos.cedula_trabajador}</small>
                        </div>
                        
                        <div class="firma-box">
                            <br><br>
                            <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                            <p style="margin-top: 5px;"><strong>Recursos Humanos</strong></p>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #999;">
                        Fecha Original: ${fechaRecibo} | COPIA FIEL
                    </div>
                </div>
            `;
            
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
