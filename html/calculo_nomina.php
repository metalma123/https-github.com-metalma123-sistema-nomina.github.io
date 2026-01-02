<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

include '../php/conexion.php';

$nro_recibo = "000000"; 

try {
    // Consultamos el último ID insertado para calcular el siguiente
    $stmt = $conexion->query("SELECT MAX(id_recibo) as max_id FROM nomina");
    if ($stmt) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si hay registros, sumamos 1. Si no (null), el primero será el 1.
        $next_id = ($row && $row['max_id']) ? $row['max_id'] + 1 : 1;
        $nro_recibo = $next_id;
    }
} catch (Exception $e) {
    // Si falla la conexión, $nro_recibo se queda en "000000" para no romper la página
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema De Calculo De Nomina</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos de Impresión (Mantener intactos) */
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

        /* Ajustes específicos para este formulario complejo */
        .seccion { margin-bottom: 20px; padding: 15px; background: rgba(255,255,255,0.5); border-radius: 8px; }
        .grid-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .totales-resaltados { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
 <div class="container">
    <!-- Encabezado del Recibo -->
    <!-- Encabezado -->
    <h2 style="margin-top:0;">Cálculo de Nómina</h2>
    <div style="text-align: right; margin-bottom: 20px;">
        <strong>RECIBO N°:</strong> <span style="color:red; font-size:18px;"><?php echo str_pad($nro_recibo, 6, "0", STR_PAD_LEFT); ?></span>
    </div>

    <!-- Buscador -->
    <div class="no-print seccion" style="background:#e9ecef; margin-bottom:20px;">
        <label>Buscar Trabajador por Cédula:</label>
        <div style="display:flex; gap:10px;">
            <input type="text" id="busqueda_cedula" placeholder="Ingrese cédula...">
            <button type="button" class="btn-calc" onclick="buscarEmpleado()">Cargar Datos</button>
        </div>
    </div>

    <form id="formNomina" action="../php/nomina.php" method="POST">
        <div class="grid-form">
            <!-- Datos del Empleado -->
            <div class="seccion">
                <h3>Datos Personales</h3>
                <label>Nombres y Apellidos:</label>
                <input type="text" id="nombre_completo" readonly style="background:#eee;">
                <label>Cargo:</label>
                <input type="text" id="cargo" readonly style="background:#eee;">
                <label>Cédula:</label>
                <input type="text" name="cedula" id="cedula_val" readonly style="background:#eee;" required>
            </div>

            <!-- Cálculos de Pago -->
            <div class="seccion">
                <h3>Cálculo de Jornada</h3>
                <label>Días Trabajados:</label>
                <input type="number" id="dias" name="dias" value="0">
                <label>Valor Día ($):</label>
                <input type="number" step="0.01" id="v_dia" name="v_dia" value="0">
                <label>Horas Extras (Cant.):</label>
                <input type="number" id="h_extras" name="h_extras" value="0">
                <label>Valor Hora Extra ($):</label>
                <input type="number" step="0.01" id="v_h_extra" name="v_h_extra" value="0">
            </div>

            <div class="seccion">
                <h3>Bonos y Retenciones</h3>
                <label>Bonus Especial ($):</label>
                <input type="number" step="0.01" id="bonus" name="bonus" value="0">
                <label>Seguro Social ($):</label>
                <input type="number" step="0.01" id="seguro" name="seguro" value="0">
                <label>Préstamos / Otros ($):</label>
                <input type="number" step="0.01" id="prestamos" name="prestamos" value="0">
                <label>Tasa de Cambio (Bs):</label>
                <input type="number" step="0.01" name="tasa" id="tasa" oninput="calcular()">
            </div>

            <div class="seccion">
                <h3>Observaciones</h3>
                <textarea id="observaciones" name="observaciones" rows="5" placeholder="Notas adicionales del pago..."></textarea>
                <button type="button" class="btn-clear" style="margin-top:10px; width:100%;" onclick="document.getElementById('observaciones').value=''">Limpiar Observación</button>
            </div>
        </div>

        <!-- Resultados Totales -->
        <div class="totales-resaltados">
            <div style="display:flex; justify-content:space-around;">
                <div>
                    <h4 style="margin:0;">TOTAL A PAGAR (USD)</h4>
                    <input type="text" id="total_usd" name="total_usd" readonly style="font-size:24px; text-align:center; border:none; color:#007bff; background:transparent;">
                </div>
                <div>
                    <h4 style="margin:0;">TOTAL A PAGAR (BS)</h4>
                    <input type="text" id="total_bs" name="total_bs" readonly style="font-size:24px; text-align:center; border:none; color:#28a745; background:transparent;">
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="btn-group no-print" style="margin-top: 20px;">
            <button type="button" class="btn-modificar" onclick="calcularNomina()">Calcular Montos</button>
            <button type="button" class="btn-save" onclick="guardarYImprimir()">Guardar e Imprimir</button>
            <button type="button" class="btn-clear" onclick="limpiarTodo()">Limpiar Todo</button>
            <button type="button" class="btn-back" onclick="location.href='menu.php'">Volver al Menú</button>
            <button type="button" class="btn-delete" style="margin-top:10px;" onclick="location.href='../php/logout.php'">Cerrar Sesión</button>
        </div>

        <!-- Sección de Firmas (Solo Impresión) -->
        <div class="print-only firma-box">
            <div style="text-align:center;">
                <br>__________________________<br>
                Firma del Trabajador<br>
                C.I: <span id="p_cedula"></span>
            </div>
            <div style="text-align:center;">
                <div class="sello-empresa">SELLO</div>
                <br>__________________________<br>
                Recursos Humanos
            </div>
        </div>
    </form>
</div>

<!-- Contenedor oculto para la impresión (Fuera del container principal para evitar conflictos CSS) -->
<div id="printable-receipt" class="print-section">
    <!-- El contenido se inyecta aquí mediante JavaScript -->
</div>

<script>
function buscarEmpleado() {
    const cedula = document.getElementById('busqueda_cedula').value;
    if(!cedula) return alert("Ingrese una cédula");

    fetch(`../php/buscar_trabajador.php?cedula=${cedula}`)
    .then(r => r.json())
    .then(data => {
        if(data.error) alert(data.error);
        else {
            document.getElementById('nombre_completo').value = data.nombres + " " + data.apellidos;
            document.getElementById('cargo').value = data.cargo;
            document.getElementById('cedula_val').value = data.cedula;
            document.getElementById('p_cedula').innerText = data.cedula;
        }
    });
}

function calcularNomina() {
    const diasEl = document.getElementById('dias');
    const vDiaEl = document.getElementById('v_dia');
    const tasaEl = document.getElementById('tasa');

    // Validación: Verificar que los campos no estén vacíos
    if (!diasEl.value || !vDiaEl.value || !tasaEl.value) {
        alert("Debe llenar los campos obligatorios: Días Trabajados, Valor Día y Tasa de Cambio");
        return false;
    }

    const dias = parseFloat(diasEl.value) || 0;
    const v_dia = parseFloat(vDiaEl.value) || 0;
    const horas = parseFloat(document.getElementById('h_extras').value) || 0;
    const v_hora = parseFloat(document.getElementById('v_h_extra').value) || 0;
    const bonus = parseFloat(document.getElementById('bonus').value) || 0;
    const seguro = parseFloat(document.getElementById('seguro').value) || 0;
    const prestamos = parseFloat(document.getElementById('prestamos').value) || 0;
    const tasa = parseFloat(document.getElementById('tasa').value) || 0;

    const subtotal = (dias * v_dia) + (horas * v_hora) + bonus;
    const deducciones = seguro + prestamos;
    const neto_usd = subtotal - deducciones;
    const neto_bs = neto_usd * tasa;

    document.getElementById('total_usd').value = neto_usd.toFixed(2);
    document.getElementById('total_bs').value = neto_bs.toFixed(2);
    
    return true;
}

function limpiarTodo() {
    if(confirm("¿Desea limpiar todos los campos?")) {
        document.getElementById('formNomina').reset();
        document.getElementById('nombre_completo').value = "";
        document.getElementById('cargo').value = "";
        document.getElementById('cedula_val').value = "";
        document.getElementById('busqueda_cedula').value = "";
    }
}

function guardarYImprimir() {
    // 1. Validar campos básicos
    const cedula = document.getElementById('cedula_val').value;
    if (!cedula) return alert("Debe seleccionar un trabajador primero.");
    
    // Calcular de nuevo por seguridad y validar
    if (!calcularNomina()) {
        return;
    }

    // 2. Preparar datos para enviar
    const form = document.getElementById('formNomina');
    const formData = new FormData(form);

    // 3. Enviar datos via AJAX a nomina.php
    // IMPORTANTE: Hemos modificado nomina.php para aceptar ?ajax=1
    fetch('../php/nomina.php?ajax=1', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // 4. Si se guardó correctamente, preparar el recibo
            const datosRecibo = {
                id_recibo: data.id_recibo || 'N/A', // Usamos el ID devuelto por el servidor
                nombres: document.getElementById('nombre_completo').value.split(' ')[0], // Simplificación
                apellidos: document.getElementById('nombre_completo').value.split(' ')[1] || '',
                cedula_trabajador: cedula,
                cargo: document.getElementById('cargo').value,
                dias_trabajados: formData.get('dias'),
                valor_dia_usd: formData.get('v_dia'),
                horas_extras: formData.get('h_extras'),
                valor_hora_extra_usd: formData.get('v_h_extra'),
                bonus_usd: formData.get('bonus'),
                seguro_social_usd: formData.get('seguro'),
                prestamos_usd: formData.get('prestamos'),
                tasa_cambio_bs: formData.get('tasa'),
                total_usd: formData.get('total_usd'),
                total_bs: formData.get('total_bs'),
                observaciones: formData.get('observaciones')
            };

            // 5. Generar HTML y abrir ventana de impresión
            imprimirRecibo(datosRecibo);

        } else {
            alert("Error al guardar: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Ocurrió un error al intentar guardar.");
    });
}

function imprimirRecibo(datos) {
    const printDiv = document.getElementById('printable-receipt');
    
    // Obtener fecha y hora actual formateada
    const now = new Date();
    const dia = String(now.getDate()).padStart(2, '0');
    const mes = String(now.getMonth() + 1).padStart(2, '0');
    const anio = now.getFullYear();
    const hora = now.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
    const fechaImpresion = `${dia}/${mes}/${anio} ${hora}`;

    // Plantilla del recibo con estilos mejorados
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
            
            <!-- Encabezado con Logo -->
            <div class="header-logo">
                <div style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">LOGO EMPRESA</div>
                <h2 style="margin: 5px 0;">SISTEMA DE NÓMINA 2026</h2>
                <h3 style="margin: 5px 0; color: #555;">Recibo de Pago de Sonmetal</h3>
                <p style="font-size: 12px; margin: 5px 0;">Recibo N°: <span style="color:red; font-size:16px;">${String(datos.id_recibo).padStart(6, '0')}</span></p>
            </div>

            <hr style="border: 1px solid #ccc;">

            <!-- Datos del Trabajador -->
            <div style="margin-bottom: 15px;">
                <p><strong>Nombres y Apellidos:</strong> ${datos.nombres} ${datos.apellidos}</p>
                <p><strong>Cédula de Identidad:</strong> ${datos.cedula_trabajador}</p>
                <p><strong>Cargo:</strong> ${datos.cargo}</p>
            </div>

            <!-- Tabla de Detalles -->
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

            <!-- Totales -->
            <div style="text-align: right; font-size: 16px;">
                <p><strong>Tasa de Cambio:</strong> ${datos.tasa_cambio_bs} Bs/USD</p>
                <h3 style="margin: 5px 0; color: #007bff;">Total a Pagar (USD): ${datos.total_usd} $</h3>
                <h3 style="margin: 5px 0; color: #28a745;">Total a Pagar (Bs): ${datos.total_bs} Bs</h3>
            </div>

            <p style="margin-top: 10px; font-style: italic;"><strong>Observaciones:</strong> ${datos.observaciones || 'Sin observaciones'}</p>

            <!-- Sección de Firmas -->
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
                Generado el: ${fechaImpresion}
            </div>
        </div>
    `;
    
    // Retraso ligero para asegurar carga de estilos
    setTimeout(() => {
        window.print();
    }, 500);
}

</script>
</body>
</html>