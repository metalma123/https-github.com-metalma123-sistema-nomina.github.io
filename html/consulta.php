<?php
require_once '../php/auth.php';
include('../php/consulta_logica.php'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Recibos de Nómina</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos de Impresión */
        @media print {
            body * { visibility: hidden; }
            .print-section, .print-section * { visibility: visible; }
            .print-section { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                padding: 0; 
                margin: 0;
                box-sizing: border-box;
                display: block !important; 
                background: white;
            }
            .no-print { display: none !important; }
            
            /* Ajustes para textos en impresión */
            h2, h3, p, td, th { color: #000 !important; }
        }
        #printable-receipt { display: none; }
        
        /* Ajustes tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddddddff; padding: 10px; text-align: left; background: rgba(255,255,255,0.8); }
        th { background-color: #5e4545b4; color: white; }

        /* Estilo para botón de imprimir */
        .btn-print {
            background: linear-gradient(45deg, #FF9800, #F57C00);
            color: white;
            padding: 5px 10px; /* Más compacto */
            font-size: 14px;
            width: auto; /* No ocupar todo el ancho */
            display: inline-block;
            margin-top: 0;
        }

        /* Estilos Responsivos para Móvil */
        @media (max-width: 600px) {
            h2 { font-size: 1.2rem; }
            h3 { font-size: 1rem; }
            
            table, th, td {
                font-size: 12px; /* Letra más pequeña */
                padding: 5px; /* Menos relleno */
            }
            
            .btn-print {
                padding: 4px 8px;
                font-size: 12px;
            }
            
            /* Ajustar inputs para que no se vean gigantes */
            input[type="text"] {
                padding: 8px;
                font-size: 14px;
            }
            
            .btn {
                padding: 10px;
                font-size: 14px;
            }
            
            .desktop-only {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container no-print">
    <h2>Consulta de Recibos de Pago</h2>
    
    <form method="POST" class="search-box" style="flex-wrap: wrap;" onsubmit="return validarBusqueda()">
        <input type="text" id="b_cedula" name="cedula" placeholder="Buscar por Cédula" value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
        <input type="text" id="b_recibo" name="id_recibo" placeholder="Buscar por ID Recibo" value="<?php echo htmlspecialchars($_POST['id_recibo'] ?? ''); ?>">
        
        <div style="width: 100%; display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" name="submit_search" class="btn btn-search">Buscar Recibos</button>
            <button type="button" onclick="window.location.href='consulta.php'" class="btn btn-clear">Limpiar</button>
        </div>
    </form>
    
    <script>
    function validarBusqueda() {
        const cedula = document.getElementById('b_cedula').value.trim();
        const recibo = document.getElementById('b_recibo').value.trim();
        
        if (cedula === "" && recibo === "") {
            alert("Debe llenar al menos un campo para realizar la búsqueda (Cédula o ID Recibo).");
            return false;
        }
        return true;
    }
    </script>
    
    <div style="margin-top: 20px; display: flex; gap: 10px;">
        <button onclick="location.href='menu.php'" class="btn btn-back">Volver al Menú</button>
        <button onclick="location.href='index.html'" class="btn btn-delete">Cerrar Sesión</button>
    </div>

    <?php if (!empty($resultados_html)): ?>
        <h3>Resultados para: <?php echo htmlspecialchars($trabajador_nombre); ?></h3>
        <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID Recibo</th>
                    <th>Fecha Registro</th>
                    <th>Total (USD)</th>
                    <th>Total (Bs)</th>
                    <th class="no-print">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $resultados_html; // Insertar los resultados generados por PHP ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<!-- Contenedor oculto para la impresión -->
<div id="printable-receipt" class="print-section">
    <!-- El contenido se inyecta aquí mediante JavaScript -->
</div>

<script>
function prepararImpresion(datos) {
    const printDiv = document.getElementById('printable-receipt');
    
    // Formato simple de recibo usando los campos especificados
    printDiv.innerHTML = `
        <div style="border: 2px solid #000; padding: 20px; width: 100%; max-width: 800px; margin: 0 auto; box-sizing: border-box; font-family: Arial, sans-serif;">
            <h2 style="text-align: center;">SISTEMA DE NOMINA 2025</h2>
            <h3 style="text-align: center;">Recibo de Pago #${datos.id_recibo}</h3>
            <p><strong>Nombres y Apellidos:</strong> ${datos.nombres} ${datos.apellidos}</p>
            <p><strong>Cédula:</strong> ${datos.cedula_trabajador} | <strong>Cargo:</strong> ${datos.cargo}</p>
            <hr>
            <table>
                <tr><td>Días Trabajados:</td><td>${datos.dias_trabajados}</td></tr>
                <tr><td>Valor Día:</td><td>${datos.valor_dia_usd} USD</td></tr>
                <tr><td>Horas Extras:</td><td>${datos.horas_extras}</td></tr>
                <tr><td>Valor Hora Extra:</td><td>${datos.valor_hora_extra_usd} USD</td></tr>
            </table>
            <hr>
            <table>
                <tr><td>Bono Especial:</td><td>+ ${datos.bonus_usd} USD</td></tr>
                <tr><td>Seguro Social:</td><td>- ${datos.seguro_social_usd} USD</td></tr>
                <tr><td>Préstamos / Otros:</td><td>- ${datos.prestamos_usd} USD</td></tr>
            </table>
            <hr>
            <p><strong>Tasa de Cambio (Bs/USD):</strong> ${datos.tasa_cambio_bs}</p>
            <h3>TOTAL A PAGAR (USD): ${datos.total_usd}</h3>
            <h3>TOTAL A PAGAR (BS): ${datos.total_bs}</h3>
            <p><strong>Observaciones:</strong> ${datos.observaciones || 'Ninguna'}</p>
            <br><br>
            <div style="text-align: center;">
                <p>__________________________</p>
                <p>Firma del Trabajador</p>
            </div>
        </div>
    `;
    
    // Activar la ventana de impresión del navegador
    window.print();

    // Ocultar el recibo de nuevo después de cerrar el diálogo de impresión (esto es automático en la mayoría de navegadores)
    // printDiv.style.display = 'none'; // No es estrictamente necesario, el media query maneja la visibilidad
}
</script>

</body>
</html>