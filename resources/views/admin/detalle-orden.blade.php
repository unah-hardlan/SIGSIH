<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Servicio - ACF Technologies</title>
    <link rel="stylesheet" href="{{ asset('css/ordenes-servicio.css') }}">


</head>


<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<body>

    <div class="container">
        <header style="margin-bottom:10px;">
            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                <tr>
                    <td style="width:220px; vertical-align:middle; padding:0;">
                        <img src="{{ asset('images/LOGO_ACF.jpg') }}" alt="Logo ACF Technologies"
                            style="width:220px; height:auto; display:block;">
                    </td>
                    <td
                        style="width:400px; text-align:center; vertical-align:middle; font-weight:bold; font-size:13px; padding:0 5px;">
                        Col. Las Mercedes, Av. Los Espliegos y Calle<br>
                        Los Eucaliptos N°10, San Salvador, El Salvador,<br>
                        Tel. +503 2209-9400<br>
                        supportlat@acftechnologies.com<br>
                        www.acftechnologies.com
                    </td>
                    <td style="width:270px; padding:0;"></td>
                </tr>
            </table>
        </header>

        <div style="display:flex; justify-content:flex-end; margin-bottom:15px;">
            <table class="solicitud-table">
                <thead>
                    <tr>
                        <th>Nº de Solicitud ACF</th>
                        <th>Nº de Solicitud Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="num-solicitud-acf">—</td>
                        <td id="num-solicitud-cliente">—</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="main-title" style="margin:-10px 0 30px;">
            ORDEN DE SERVICIO
        </div>

        <div style="display:flex; justify-content:flex-start; gap:19em; align-items:flex-start; margin-bottom:30px;">

            <table class="dates-table">
                <tr>
                    <th>FECHA DE RECEPCION</th>
                    <th>HORA DE RECEPCION</th>
                </tr>
                <tr>
                    <td id="fecha-recepcion">—</td>
                    <td id="hora-recepcion">—</td>
                </tr>
                <tr>
                    <th>FECHA DE INICIO</th>
                    <th>HORA DE INICIO</th>
                </tr>
                <tr>
                    <td id="fecha-inicio">—</td>
                    <td id="hora-inicio">—</td>
                </tr>
                <tr>
                    <th>FECHA DE CULMINACION</th>
                    <th>HORA DE CULMINACION</th>
                </tr>
                <tr>
                    <td id="fecha-fin">—</td>
                    <td id="hora-fin">—</td>
                </tr>
            </table>

            <table class="estado-tabla">
                <thead>
                    <tr>
                        <th colspan="4">ESTADO DE LA ORDEN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ABIERTA</td>
                        <td class="checkbox-cell"><input id="estado-abierta" type="checkbox"></td>
                        <td>CERRADA</td>
                        <td class="checkbox-cell"><input id="estado-cerrada" type="checkbox"></td>
                    </tr>
                </tbody>
            </table>

        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td colspan="2"
                        style="padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-right: none;">
                        <div class="field-header">CLIENTE:</div>
                        <div id="cliente-nombre" style="height: 25px;">—</div>
                    </td>
                    <td colspan="2" style="padding: 0; vertical-align: top; border: 1px solid var(--border-color);">
                        <div class="field-header">CONTACTO</div>
                        <div id="contacto-nombre" style="height: 25px;">—</div>
                    </td>
                </tr>
                <tr>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">CIUDAD</div>
                        <div id="cliente-ciudad" style="height: 25px;">—</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">OFICINA</div>
                        <div id="cliente-oficina" style="height: 25px;">—</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">TELEFONOS</div>
                        <div id="cliente-telefonos" style="height: 25px;">—</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none;">
                        <div class="field-header">CORREO ELECTRONICO</div>
                        <div id="cliente-correo" style="height: 25px;">—</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"
                        style="padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none;">
                        <div class="field-header">DIRECCION</div>
                        <div id="cliente-direccion" style="height: 25px;">—</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table
            style="width: 100%; border-collapse: collapse; border: 1px solid var(--border-color); margin-bottom: 15px;">
            <tbody>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0;">
                        <div class="field-header">DESCRIPCION DEL SERVICIO / FALLA (CLIENTE):</div>
                        <div id="desc-cliente" class="field-content-large">—</div>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 0;">
                        <div class="field-header">DESCRIPCION DEL SERVICIO / FALLA (PERSONAL ACF):</div>
                        <div id="desc-acf" class="field-content-large">—</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="field-header">ACTIVIDAD REALIZADA PARA LA SOLUCIÓN (ADJUNTAR FOTOS/VIDEO DE LA
                            ACTIVIDAD):</div>
                        <div id="actividad" class="field-content-large">—</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="repuesto-table" style="margin-bottom: 0;">
            <tr>
                <th style="width: 30%;">SE INSTALO ALGUN REPUESTO:</th>
                <td>
                    <span class="checkbox-label">SI <input id="repuesto-si" type="checkbox" style="vertical-align: middle;"></span>
                    <span class="checkbox-label">NO <input id="repuesto-no" type="checkbox" style="vertical-align: middle;"></span>
                    <span>CUAL: <span id="repuesto-cual" style="display: inline-block; width: 70%;">—</span></span>
                </td>
            </tr>
        </table>

        <div style="height: 15px;"></div>

        <table class="repuesto-table" style="margin-bottom: 15px;">
            <tr>
                <th style="width: 30%;">CALIFICACION DEL SERVICIO</th>
                <td>
                    <span class="checkbox-label">EXCELENTE <input type="checkbox" style="vertical-align: middle;"
                            checked></span>
                    <span class="checkbox-label">BUENO <input type="checkbox" style="vertical-align: middle;"></span>
                    <span class="checkbox-label">REGULAR <input type="checkbox" style="vertical-align: middle;"></span>
                    <span class="checkbox-label">DEFICIENTE <input type="checkbox"
                            style="vertical-align: middle;"></span>
                </td>
            </tr>
        </table>

        <div style="display: flex; justify-content: space-between; gap: 25px; margin-top: 15px; margin-bottom: 15px;">
            <!-- Firma Cliente -->
            <table class="firma-table" style="width: 48%;">
                <tr>
                    <th>CLIENTE:</th>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="firma-label">NOMBRE Y APELLIDO</div>
                        <div id="firma-cliente-nombre" class="firma-space">—</div>
                        <div class="firma-label">C.I.</div>
                        <div id="firma-cliente-ci" class="firma-space">—</div>
                        
                        <div class="firma-space">Gerente de Operaciones</div>
                        <div class="firma-label">FIRMA</div>
                        <div class="firma-space-larger"></div>
                    </td>
                </tr>
            </table>

            <table class="firma-table" style="width: 48%;">
                <tr>
                    <th>ACF TECHNOLOGIES</th>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="firma-label">NOMBRE Y APELLIDO</div>
                        <div id="firma-tecnico-nombre" class="firma-space">—</div>
                        <div class="firma-label">C.I.</div>
                        <div id="firma-tecnico-ci" class="firma-space">—</div>
                        
                        <div class="firma-space">Técnico ACF</div>
                        <div class="firma-label">FIRMA</div>
                        <div class="firma-space-larger"></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <!-- Botón Generar PDF (solo Tailwind, no sale al imprimir) -->
    <div class="no-print my-10 flex justify-center">
        <button onclick="window.print()"
            class="bg-blue-900 text-white px-8 py-3 rounded-xl shadow-lg hover:bg-blue-700 text-lg font-bold transition">
            Generar PDF
        </button>
    </div>

    </div>

    <script>
        (function () {
            const qs = new URLSearchParams(location.search);
            const id = qs.get('orden');
            if (!id) {
                console.warn('Falta el parámetro ?orden=ID');
                return;
            }
            const $ = (id) => document.getElementById(id);
            const setText = (id, value) => {
                const el = $(id);
                if (!el) return;
                const v = (value ?? '—');
                el.textContent = (typeof v === 'string') ? v.trim() || '—' : v;
            };
            const parseDate = (val) => {
                if (!val) return { d: '—', t: '—' };
                try {
                    const dt = new Date(val);
                    if (!isNaN(dt.getTime())) {
                        const d = dt.toISOString().slice(0, 10);
                        const t = dt.toTimeString().slice(0,5);
                        return { d, t };
                    }
                    // Fallback "YYYY-MM-DD HH:MM:SS"
                    const [d, rest] = String(val).split(' ');
                    const t = (rest || '').slice(0,5) || '—';
                    return { d: d || '—', t };
                } catch (_) { return { d: '—', t: '—' }; }
            };
            const nombresPersona = (p) => {
                if (!p) return '';
                return [p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido]
                    .filter(Boolean).join(' ').trim();
            };

            fetch('/api/ordenes-servicio/' + encodeURIComponent(id), { headers: { 'Accept': 'application/json' } })
                .then(async (res) => {
                    if (res.status === 401) throw new Error('No autorizado');
                    if (!res.ok) throw new Error('Error al cargar la orden');
                    const json = await res.json();
                    const data = json.data || json;

                    // Encabezado números de solicitud
                    const sol = data.solicitud_servicio || data.solicitud || {};
                    setText('num-solicitud-acf', sol.numero_solicitud_acf || '—');
                    setText('num-solicitud-cliente', sol.numero_solicitud_cliente || '—');

                    // Fechas
                    const fr = parseDate(data.fecha_recepcion);
                    const fi = parseDate(data.fecha_inicio);
                    const ff = parseDate(data.fecha_finalizacion);
                    setText('fecha-recepcion', fr.d); setText('hora-recepcion', fr.t);
                    setText('fecha-inicio', fi.d); setText('hora-inicio', fi.t);
                    setText('fecha-fin', ff.d); setText('hora-fin', ff.t);

                    // Estado
                    const estado = data.estado || {};
                    const codigo = (estado.codigo || estado.nombre || '').toString().toLowerCase();
                    const cerrada = codigo.includes('cerr') || !!data.fecha_finalizacion;
                    const abierta = !cerrada;
                    const chkA = $('estado-abierta'); const chkC = $('estado-cerrada');
                    if (chkA) chkA.checked = abierta;
                    if (chkC) chkC.checked = cerrada;

                    // Cliente y contacto
                    const cliente = (sol.cliente || {});
                    const empresa = cliente.empresa || {};
                    const clienteNombre = empresa.nombre_comercial || empresa.razon_social || cliente.nombre || '';
                    setText('cliente-nombre', clienteNombre || '—');
                    const contacto = sol.contacto || {};
                    // Nombre de contacto: si viene junto al cliente.persona o no disponible
                    const contactoNombre = contacto.nombre || '';
                    setText('contacto-nombre', contactoNombre || '—');
                    // Tel/Correo a partir del contacto si su tipo lo indica
                    const tipo = (contacto.tipo_contacto || '').toLowerCase();
                    if (tipo.includes('mail')) setText('cliente-correo', contacto.valor_contacto);
                    if (tipo.includes('tel')) setText('cliente-telefonos', contacto.valor_contacto);

                    // Dirección / Ciudad / Oficina (si están disponibles)
                    setText('cliente-direccion', empresa.direccion || cliente.direccion || '');
                    setText('cliente-ciudad', empresa.ciudad || cliente.ciudad || '');
                    setText('cliente-oficina', empresa.oficina || '');

                    // Descripciones
                    // Preferir lo que el cliente declaró en la orden; si no, caer a la solicitud
                    setText('desc-cliente', (data.diagnostico_cliente && data.diagnostico_cliente.trim()) ? data.diagnostico_cliente : (sol.descripcion_problema || ''));
                    setText('desc-acf', data.diagnostico_tecnico || '');
                    setText('actividad', data.observaciones || '');

                    // Firmas
                    // Cliente: intentar con empresa/persona
                    setText('firma-cliente-nombre', clienteNombre || '');
                    setText('firma-cliente-ci', empresa.rtn || '');
                    // Técnico
                    const tecnico = data.tecnico || {};
                    setText('firma-tecnico-nombre', nombresPersona(tecnico) || '');
                    setText('firma-tecnico-ci', tecnico.dni || '');

                    // Repuestos (si se expone un array detalles_orden_producto)
                    const detalles = data.detalles_producto || data.detalle_orden_producto || [];
                    if (Array.isArray(detalles) && detalles.length) {
                        const si = $('repuesto-si'); const no = $('repuesto-no');
                        if (si) si.checked = true; if (no) no.checked = false;
                        const nombres = detalles.map(d => d.producto_nombre || d.nombre || d.repuesto || '').filter(Boolean);
                        setText('repuesto-cual', nombres.join(', '));
                    } else {
                        const si = $('repuesto-si'); const no = $('repuesto-no');
                        if (si) si.checked = false; if (no) no.checked = true;
                    }
                })
                .catch((e) => {
                    console.error(e);
                    alert('No se pudo cargar la orden. Verifica tu sesión o el ID.');
                });
        })();
    </script>

</body>

</html>