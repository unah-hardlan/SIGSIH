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
                        <td class="checkbox-cell"><input id="estado-abierta" type="checkbox" disabled></td>
                        <td>CERRADA</td>
                        <td class="checkbox-cell"><input id="estado-cerrada" type="checkbox" disabled></td>
                    </tr>
                </tbody>
            </table>

        </div>

        @php
        // Valores calculados por servidor si se pasó $orden. Si no, la UI JS seguirá funcionando
        $o = $orden ?? null;

        // Si no se pasó $orden desde el controlador, intentar cargarlo usando el parámetro ?orden=ID
        if (!$o && request()->has('orden')) {
        try {
        $id = request()->get('orden');
        $o = \App\Models\OrdenServicio::with([
        'solicitudServicio.cliente.empresa',
        'solicitudServicio.cliente.contactos',
        'solicitudServicio.contacto',
        // personas -> usuario (tbl_ms_usuario) ya que el correo puede venir del usuario ligado a la persona
        'solicitudServicio.cliente.personas.usuario',
        'solicitudServicio.cliente.agencias.direccion.ciudad',
        ])->find($id);
        } catch (\Throwable $e) {
        // No bloquear la vista; dejamos $o null y JS seguirá llenando los datos.
        $o = null;
        }
        }

        if ($o) {
        // Nombre cliente: preferir empresa; si no existe, construir nombre desde la persona
        $clienteNombre = data_get($o, 'solicitudServicio.cliente.empresa.nombre_comercial')
        ?: data_get($o, 'solicitudServicio.cliente.empresa.razon_social');

        if (!$clienteNombre) {
        // Intentar tomar la primera persona ligada al cliente y concatenar sus partes del nombre
        $personasTmp = data_get($o, 'solicitudServicio.cliente.personas', []);
        $personaCandidate = null;
        if (!empty($personasTmp)) {
        if (is_array($personasTmp)) {
        $personaCandidate = $personasTmp[0] ?? null;
        } else {
        // colección (Eloquent)
        $personaCandidate = count($personasTmp) ? $personasTmp->first() : null;
        }
        }

        if ($personaCandidate) {
        $pn = trim(implode(' ', array_filter([
        data_get($personaCandidate, 'primer_nombre'),
        data_get($personaCandidate, 'segundo_nombre'),
        data_get($personaCandidate, 'primer_apellido'),
        data_get($personaCandidate, 'segundo_apellido'),
        ])));
        // si no se pudo construir, intentar campos alternativos
        $clienteNombre = $pn ?: data_get($personaCandidate, 'full_name') ?: data_get($personaCandidate,
        'nombre_completo');
        }
        }

        $clienteNombre = $clienteNombre ?: '—';

        // Contacto principal (de la solicitud)
        // Preferir nombre completo si está disponible; si no, intentar persona ligada al cliente;
        // si aún no hay, buscar en los contactos del cliente un valor textual (no teléfono).
        $contactoNombre = '—';
        $solContacto = data_get($o, 'solicitudServicio.contacto', []);
        if (!empty($solContacto)) {
        // intentar campos explícitos de nombre primero
        $nombreFromContacto = data_get($solContacto, 'nombre') ?: data_get($solContacto, 'valor_contacto');
        if ($nombreFromContacto && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', (string) $nombreFromContacto)) {
        $contactoNombre = trim((string) $nombreFromContacto);
        }
        }

        // Si todavía no existe un nombre significativo, intentar con las personas del cliente
        if (empty($contactoNombre) || $contactoNombre === '—') {
        $personasTmp = data_get($o, 'solicitudServicio.cliente.personas', []);
        $personaCandidate = null;
        if (!empty($personasTmp)) {
        if (is_array($personasTmp)) {
        $personaCandidate = $personasTmp[0] ?? null;
        } else {
        $personaCandidate = count($personasTmp) ? $personasTmp->first() : null;
        }
        }
        if ($personaCandidate) {
        $pn = trim(implode(' ', array_filter([
        data_get($personaCandidate, 'primer_nombre'),
        data_get($personaCandidate, 'segundo_nombre'),
        data_get($personaCandidate, 'primer_apellido'),
        data_get($personaCandidate, 'segundo_apellido'),
        ])));
        if ($pn) $contactoNombre = $pn;
        }
        }

        // Último recurso: recorrer los contactos del cliente y elegir el primer valor textual (no teléfono)
        if (empty($contactoNombre) || $contactoNombre === '—') {
        $ct = data_get($o, 'solicitudServicio.cliente.contactos', []);
        if (!empty($ct)) {
        foreach ($ct as $c) {
        $valor = trim((string) data_get($c, 'valor_contacto', ''));
        if ($valor !== '' && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', $valor)) {
        $contactoNombre = $valor;
        break;
        }
        }
        }
        }
        $contactoNombre = $contactoNombre ?: '—';

        // Teléfonos desde contactos del cliente (soportar colección o array)
        $telefonosVal = '—';
        $ct = data_get($o, 'solicitudServicio.cliente.contactos', []);
        $phones = [];
        if (!empty($ct)) {
        foreach ($ct as $c) {
        // soportar tanto arrays como objetos
        $tipo = strtolower(trim(data_get($c, 'tipo_contacto', '')));
        $valor = data_get($c, 'valor_contacto', '');
        if (in_array($tipo, ['tel','telefono','phone','movil','celular','whatsapp','wa'])) {
        if ($valor) $phones[] = $valor;
        }
        }
        $phones = array_unique(array_filter($phones));
        if (count($phones)) $telefonosVal = implode(', ', $phones);
        }

        // Correo: primero desde contactos, luego desde la persona->usuario (tbl_ms_usuario)
        $correoVal = '—';
        if (!empty($ct)) {
        foreach ($ct as $c) {
        $tipo = strtolower(trim(data_get($c, 'tipo_contacto', '')));
        $valor = data_get($c, 'valor_contacto', '');
        if (in_array($tipo, ['email','correo']) && $valor) { $correoVal = $valor; break; }
        }
        }

        // Si no viene por contactos, intentar obtener correo del usuario asociado a la persona del cliente
        if ($correoVal === '—') {
        $personas = data_get($o, 'solicitudServicio.cliente.personas', []);
        if (!empty($personas)) {
        foreach ($personas as $p) {
        // persona may be object or array
        $mail = data_get($p, 'usuario.correo_electronico');
        if ($mail) { $correoVal = $mail; break; }
        }
        }
        }

        // Dirección / Oficina / Ciudad: preferir agencia asociada al cliente (tbl_agencia)
        $direccionVal = '—';
        $oficinaVal = '—';
        $ciudadVal = '—';

        $agencias = data_get($o, 'solicitudServicio.cliente.agencias', []);
        if (!empty($agencias)) {
        // Tomar la primera agencia disponible (puedes ajustar la lógica si hay una agencia preferida)
        $ag = is_array($agencias) ? ($agencias[0] ?? null) : (count($agencias) ? $agencias->first() : null);
        if ($ag) {
        $oficinaVal = data_get($ag, 'nombre_agencia') ?: $oficinaVal;
        $dir = data_get($ag, 'direccion');
        if ($dir) {
        // Usar accessor direccion_completa si existe, o concatenar campos conocidos
        $direccionVal = data_get($dir, 'direccion_completa')
        ?: (trim((data_get($dir, 'calle', '') . ' ' . data_get($dir, 'numero', '') . ' ' . data_get($dir, 'colonia',
        '')))) ?: $direccionVal;
        $ciudadVal = data_get($dir, 'ciudad.nombre_ciudad') ?: $ciudadVal;
        }
        }
        }

        // Si el controlador ya pasó estas variables las respetamos (compatibilidad)
        if (!empty($direccion)) $direccionVal = $direccion;
        if (!empty($oficina)) $oficinaVal = $oficina;
        if (!empty($ciudad)) $ciudadVal = $ciudad;
        // Preparar valores para la sección de firma del cliente
        $firmaNombre = $clienteNombre ?: '—';
        // intentar RTN/CI desde empresa, si no, desde la primera persona (dni, identificacion, ci)
        $firmaCi = data_get($o, 'solicitudServicio.cliente.empresa.rtn') ?: data_get($o,
        'solicitudServicio.cliente.personas.0.dni') ?: data_get($o,
        'solicitudServicio.cliente.personas.0.identificacion') ?: data_get($o,
        'solicitudServicio.cliente.personas.0.ci') ?: '—';
        // Calificación del servicio (campo nuevo en la tabla)
        $calificacionServicio = data_get($o, 'calificacion_servicio') ?: null;
        // Repuestos: preferir campo JSON 'repuestos' si existe, si no, armar desde la relación detallesProducto
        $repuestosList = [];
        try {
        if (!empty($o->repuestos)) {
        $tmp = is_array($o->repuestos) ? $o->repuestos : json_decode($o->repuestos, true);
        if (is_array($tmp)) {
        foreach ($tmp as $r) {
        $label = data_get($r, 'nombre') ?? data_get($r, 'repuesto') ?? data_get($r, 'producto_nombre') ?? data_get($r,
        'id_producto');
        $cant = data_get($r, 'cantidad');
        $repuestosList[] = $label . ($cant ? ' x' . $cant : '');
        }
        }
        } elseif (!empty($o->detallesProducto)) {
        foreach ($o->detallesProducto as $d) {
        $prodName = data_get($d, 'producto.nombre_producto') ?? data_get($d, 'producto.nombre') ?? data_get($d,
        'id_producto_fk');
        $repuestosList[] = ($prodName ?: 'Producto') . ' x' . ($d->cantidad ?? 1);
        }
        }
        } catch (\Throwable $_) {
        // no bloquear la vista
        $repuestosList = [];
        }
        } else {
        $clienteNombre = '—';
        $contactoNombre = '—';
        $telefonosVal = '—';
        $correoVal = '—';
        $direccionVal = '—';
        $oficinaVal = '—';
        $ciudadVal = '—';
        $firmaNombre = '—';
        $firmaCi = '—';
        }
        @endphp

        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td colspan="2"
                        style="padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-right: none;">
                        <div class="field-header">CLIENTE:</div>
                        <div id="cliente-nombre" style="height: 25px;">{{ $clienteNombre }}</div>
                    </td>
                    <td colspan="2" style="padding: 0; vertical-align: top; border: 1px solid var(--border-color);">
                        <div class="field-header">CONTACTO</div>
                        <div id="contacto-nombre" style="height: 25px;">{{ $contactoNombre }}</div>
                    </td>
                </tr>
                <tr>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">CIUDAD</div>
                        <div id="cliente-ciudad" style="height: 25px;">{{ $ciudadVal }}</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">OFICINA</div>
                        <div id="cliente-oficina" style="height: 25px;">{{ $oficinaVal }}</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none; border-right: none;">
                        <div class="field-header">TELEFONOS</div>
                        <div id="cliente-telefonos" style="height: 25px;">{{ $telefonosVal }}</div>
                    </td>
                    <td
                        style="width: 25%; padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none;">
                        <div class="field-header">CORREO ELECTRONICO</div>
                        <div id="cliente-correo" style="height: 25px;">{{ $correoVal }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"
                        style="padding: 0; vertical-align: top; border: 1px solid var(--border-color); border-top: none;">
                        <div class="field-header">DIRECCION</div>
                        <div id="cliente-direccion" style="height: 25px;">{{ $direccionVal }}</div>
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
                    <span class="checkbox-label">SI <input id="repuesto-si" type="checkbox"
                            style="vertical-align: middle;" @if(!empty($repuestosList) && count($repuestosList)> 0)
                        checked="checked" @endif disabled></span>
                    <span class="checkbox-label">NO <input id="repuesto-no" type="checkbox"
                            style="vertical-align: middle;" @if(empty($repuestosList) || count($repuestosList)===0)
                            checked="checked" @endif disabled></span>
                    <span>CUAL: <span id="repuesto-cual"
                            style="display: inline-block; width: 70%;">{{ count($repuestosList) ? implode(', ', $repuestosList) : '—' }}</span></span>
                </td>
            </tr>
        </table>

        <div style="height: 15px;"></div>

        <table class="repuesto-table" style="margin-bottom: 15px;">
            <tr>
                <th style="width: 30%;">CALIFICACION DEL SERVICIO</th>
                <td>
                    <label class="checkbox-label">EXCELENTE <input id="calificacion-excelente"
                            name="calificacion_servicio" value="excelente" type="checkbox"
                            style="vertical-align: middle;" @if(!empty($calificacionServicio) &&
                            strtolower($calificacionServicio)==='excelente' ) checked @endif disabled></label>
                    <label class="checkbox-label">BUENO <input id="calificacion-bueno" name="calificacion_servicio"
                            value="bueno" type="checkbox" style="vertical-align: middle;"
                            @if(!empty($calificacionServicio) && strtolower($calificacionServicio)==='bueno' ) checked
                            @endif disabled></label>
                    <label class="checkbox-label">REGULAR <input id="calificacion-regular" name="calificacion_servicio"
                            value="regular" type="checkbox" style="vertical-align: middle;"
                            @if(!empty($calificacionServicio) && strtolower($calificacionServicio)==='regular' ) checked
                            @endif disabled></label>
                    <label class="checkbox-label">DEFICIENTE <input id="calificacion-deficiente"
                            name="calificacion_servicio" value="deficiente" type="checkbox"
                            style="vertical-align: middle;" @if(!empty($calificacionServicio) &&
                            strtolower($calificacionServicio)==='deficiente' ) checked @endif disabled></label>
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
                        <div id="firma-cliente-nombre" class="firma-space">{{ $firmaNombre }}</div>
                        <div class="firma-label">C.I.</div>
                        <div id="firma-cliente-ci" class="firma-space">{{ $firmaCi }}</div>

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

                        <!-- Script: asegurar que los checkboxes SI/NO para repuestos reflejen el texto 'CUAL' -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                try {
                                    var cualEl = document.getElementById('repuesto-cual');
                                    var siEl = document.getElementById('repuesto-si');
                                    var noEl = document.getElementById('repuesto-no');
                                    if (!cualEl || !siEl || !noEl) return;
                                    var text = (cualEl.textContent || '').trim();
                                    // Considerar '—' o cadena vacía como no hay repuestos
                                    if (text && text !== '—') {
                                        siEl.checked = true;
                                        noEl.checked = false;
                                    } else {
                                        siEl.checked = false;
                                        noEl.checked = true;
                                    }
                                } catch (e) {
                                    // no bloquear la vista por errores de JS
                                    console.error(e);
                                }
                            });
                        </script>
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
        (function() {
            const qs = new URLSearchParams(location.search);
            const id = qs.get('orden');
            if (!id) {
                console.warn('Falta el parámetro ?orden=ID');
                return;
            }
            const $ = (id) => document.getElementById(id);
            /**
             * Set text for an element by id.
             * If options.skipEmpty is true, do not overwrite the existing element text when value is null/undefined/empty string.
             */
            const setText = (id, value, options = {}) => {
                const el = $(id);
                if (!el) return;
                const skipEmpty = options.skipEmpty || false;
                // If caller requested to skip empty values, and the incoming value is empty-ish, don't overwrite
                if (skipEmpty) {
                    if (value === undefined || value === null) return;
                    if (typeof value === 'string' && value.trim() === '') return;
                }
                const v = (value ?? '—');
                el.textContent = (typeof v === 'string') ? v.trim() || '—' : v;
            };
            const parseDate = (val) => {
                if (!val) return {
                    d: '—',
                    t: '—'
                };
                try {
                    const dt = new Date(val);
                    if (!isNaN(dt.getTime())) {
                        const d = dt.toISOString().slice(0, 10);
                        const t = dt.toTimeString().slice(0, 5);
                        return {
                            d,
                            t
                        };
                    }
                    // Fallback "YYYY-MM-DD HH:MM:SS"
                    const [d, rest] = String(val).split(' ');
                    const t = (rest || '').slice(0, 5) || '—';
                    return {
                        d: d || '—',
                        t
                    };
                } catch (_) {
                    return {
                        d: '—',
                        t: '—'
                    };
                }
            };
            const nombresPersona = (p) => {
                if (!p) return '';
                return [p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido]
                    .filter(Boolean).join(' ').trim();
            };

            const isCliente = (location.pathname || '').indexOf('/cliente/') === 0;
            const endpoint = isCliente ?
                ('/cliente/ordenes/' + encodeURIComponent(id) + '/data') :
                ('/api/ordenes-servicio/' + encodeURIComponent(id));
            fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
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
                    setText('fecha-recepcion', fr.d);
                    setText('hora-recepcion', fr.t);
                    setText('fecha-inicio', fi.d);
                    setText('hora-inicio', fi.t);
                    setText('fecha-fin', ff.d);
                    setText('hora-fin', ff.t);

                    // Estado
                    const estado = data.estado || {};
                    const codigo = (estado.codigo || estado.nombre || '').toString().toLowerCase();
                    const cerrada = codigo.includes('cerr') || !!data.fecha_finalizacion;
                    const abierta = !cerrada;
                    const chkA = $('estado-abierta');
                    const chkC = $('estado-cerrada');
                    if (chkA) chkA.checked = abierta;
                    if (chkC) chkC.checked = cerrada;

                    // Cliente y contacto
                    const cliente = (sol.cliente || {});
                    const empresa = cliente.empresa || {};
                    const clienteNombre = empresa.nombre_comercial || empresa.razon_social || cliente.nombre ||
                        '';
                    // Cliente nombre: only overwrite if API provides a non-empty value so server-side render stays
                    if (clienteNombre && String(clienteNombre).trim() !== '') {
                        setText('cliente-nombre', clienteNombre, {
                            skipEmpty: true
                        });
                    }
                    const contacto = sol.contacto || {};
                    // Nombre de contacto: si viene junto al cliente.persona o no disponible
                    const contactoNombre = contacto.nombre || '';
                    // Contacto: preserve server content if API doesn't provide a name
                    if (contactoNombre && String(contactoNombre).trim() !== '') {
                        setText('contacto-nombre', contactoNombre, {
                            skipEmpty: true
                        });
                    }
                    // Tel/Correo a partir del contacto si su tipo lo indica
                    const tipo = (contacto.tipo_contacto || '').toLowerCase();
                    if (tipo.includes('mail')) setText('cliente-correo', contacto.valor_contacto, {
                        skipEmpty: true
                    });
                    if (tipo.includes('tel')) setText('cliente-telefonos', contacto.valor_contacto, {
                        skipEmpty: true
                    });

                    // Dirección / Ciudad / Oficina (si están disponibles)
                    // Dirección/Ciudad/Oficina: don't erase server-side values when API returns empty
                    setText('cliente-direccion', empresa.direccion || cliente.direccion || '', {
                        skipEmpty: true
                    });
                    setText('cliente-ciudad', empresa.ciudad || cliente.ciudad || '', {
                        skipEmpty: true
                    });
                    setText('cliente-oficina', empresa.oficina || '', {
                        skipEmpty: true
                    });

                    // Descripciones
                    // Preferir lo que el cliente declaró en la orden; si no, caer a la solicitud
                    setText('desc-cliente', (data.diagnostico_cliente && data.diagnostico_cliente.trim()) ? data
                        .diagnostico_cliente : (sol.descripcion_problema || ''));
                    setText('desc-acf', data.diagnostico_tecnico || '');
                    setText('actividad', data.observaciones || '');

                    // Firmas
                    // Cliente: intentar con empresa/persona
                    // Preserve client signature fields if server rendered them
                    setText('firma-cliente-nombre', clienteNombre || '', {
                        skipEmpty: true
                    });
                    setText('firma-cliente-ci', empresa.rtn || '', {
                        skipEmpty: true
                    });
                    // Técnico
                    const tecnico = data.tecnico || {};
                    setText('firma-tecnico-nombre', nombresPersona(tecnico) || '');
                    setText('firma-tecnico-ci', tecnico.dni || '');

                    // Repuestos (si se expone un array detalles_orden_producto)
                    const detalles = data.detalles_producto || data.detalle_orden_producto || [];
                    const si = $('repuesto-si');
                    const no = $('repuesto-no');
                    const cualEl = $('repuesto-cual');
                    if (Array.isArray(detalles) && detalles.length) {
                        // API provides explicit repuestos -> update checkbox and list
                        if (si) si.checked = true;
                        if (no) no.checked = false;
                        const nombres = detalles.map(d => d.producto_nombre || d.nombre || d.repuesto || '')
                            .filter(Boolean);
                        setText('repuesto-cual', nombres.join(', '));
                    } else {
                        // API does not provide repuestos. Preserve server-rendered value when present.
                        // Only mark NO if the server-side 'CUAL' is empty or a placeholder '—'.
                        try {
                            const current = (cualEl && (cualEl.textContent || '') || '').trim();
                            if (!current || current === '—') {
                                if (si) si.checked = false;
                                if (no) no.checked = true;
                            } else {
                                // leave existing checkbox state as rendered server-side
                            }
                        } catch (e) {
                            if (si) si.checked = false;
                            if (no) no.checked = true;
                        }
                    }

                    // Calificacion del servicio (si la API la expone)
                    const calFromApi = (data.calificacion_servicio || data.calificacionServicio || data
                        .calificacion || '').toString().toLowerCase();
                    if (calFromApi && calFromApi.trim() !== '') {
                        const cel = $('calificacion-excelente');
                        const cbu = $('calificacion-bueno');
                        const cre = $('calificacion-regular');
                        const cde = $('calificacion-deficiente');
                        if (cel) cel.checked = (calFromApi === 'excelente');
                        if (cbu) cbu.checked = (calFromApi === 'bueno');
                        if (cre) cre.checked = (calFromApi === 'regular');
                        if (cde) cde.checked = (calFromApi === 'deficiente');
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