<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura Hardlan</title>
</head>

<body>
    @php
    // Preparar datos del cliente para mostrar en el formato
    $cliente = $factura->cliente ?? null;
    $cliente_nombre = 'Sin cliente';
    $cliente_direccion = '';
    $cliente_telefono = '';
    $cliente_correo = '';
    $cliente_contacto = '';

    if ($cliente) {
    if (($cliente->tipo_cliente ?? null) === 'empresa' && $cliente->empresa) {
    $cliente_nombre = $cliente->empresa->nombre_comercial ?? $cliente->empresa->razon_social ?? $cliente_nombre;
    $cliente_direccion = $cliente->empresa->direccion ?? '';
    $cliente_telefono = $cliente->empresa->telefono ?? '';
    $cliente_correo = $cliente->empresa->correo_electronico ?? '';
    $cliente_contacto = $cliente->empresa->contacto ?? '';
    } else {
    // persona
    $persona = $cliente->persona;
    if ($persona) {
    if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
    $persona = $persona->first();
    }
    $cliente_nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?:
    $cliente_nombre;
    $cliente_direccion = $persona->direccion ?? '';
    $cliente_telefono = $persona->telefono ?? '';
    $cliente_correo = $persona->correo_electronico ?? '';
    // Prefer full name (nombre + apellido) for contacto
    $cliente_contacto = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) ?:
    ($persona->primer_nombre ?? '');
    }
    }
    }
    @endphp

    <div class="factura">
        <div class="encabezado-completo">
            <!-- IZQUIERDA: bloque gris -->
            <div class="bloque-encabezado">
                <div class="fila-superior">
                    <div>
                        <p class="titulo">FACTURA DE SERVICIOS</p>
                        <h1>HARDLAN</h1>
                    </div>
                </div>
                <div class="fila-inferior">
                    <div class="col">
                        <p><strong>RTN:</strong> 08011977009xxx</p>
                        <p><strong>CAI:</strong>
                            {{ optional($factura->cai)->codigo ?? 'CF1744-1C238E-0F4C93-61C128-794RE4-4G' }}
                        </p>
                    </div>
                    <div class="col">
                        <p><strong>P:</strong> 2227-0705</p>
                        <p><strong>M:</strong> 9877-7244</p>
                    </div>
                    <div class="col">
                        <p> Colonia Centro América Oeste, Bloque G,</p>
                        <p>Casa 17. Tegucigalpa, M. D. C., Honduras</p>
                    </div>
                </div>
            </div>

            <!-- DERECHA: bloque rojo -->
            <div class="bloque-factura">
                <h3>{{ $factura->numero ?? '---' }}</h3>
                <p><strong>FECHA:</strong>
                    {{ $factura->fecha ? \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') : ($factura->fecha ?? '') }}
                </p>
                <p><strong>Rango Autorizado:</strong><br>{{ $factura->cai->rango_inicio ?? '—' }}
                    al<br>{{ $factura->cai->rango_fin ?? '—' }}</p>

                @php
                // Probar varios nombres de campo usados en el proyecto. Priorizar campo en la factura,
                // luego fallback al CAI. Algunos lugares usan `fecha_limite` y otros `fecha_limite_emision`.
                $fecha_limite = null;
                if (!empty($factura->fecha_limite_emision)) {
                $fecha_limite = $factura->fecha_limite_emision;
                } elseif (!empty($factura->fecha_limite)) {
                $fecha_limite = $factura->fecha_limite;
                } elseif (!empty(optional($factura->cai)->fecha_limite_emision)) {
                $fecha_limite = optional($factura->cai)->fecha_limite_emision;
                } elseif (!empty(optional($factura->cai)->fecha_limite)) {
                $fecha_limite = optional($factura->cai)->fecha_limite;
                }
                @endphp
                <p><strong>Fecha límite de emisión:</strong><br>
                    {{ $fecha_limite ? (\Carbon\Carbon::parse($fecha_limite)->format('d/m/Y')) : '—' }}
                </p>
            </div>
        </div>


        <!-- DATOS CLIENTE -->
        @php
        // Totals: use stored factura values when present, otherwise compute from $detalles
        // Compute base subtotal (without impuestos) and total impuestos from detalles
        $computedBaseSubtotal = 0.0;
        $computedTotalImpuesto = 0.0;
        if (!empty($detalles) && is_iterable($detalles)) {
        foreach ($detalles as $d) {
        $qty = (float) ($d->cantidad ?? $d->horas ?? 1);
        $precio = (float) ($d->precio_unitario ?? ($d->precio ?? 0));
        $desc = (float) ($d->descuento ?? 0);
        $impLinea = (float) ($d->impuesto ?? 0);

        // base line subtotal (precio * qty - descuento)
        $baseLine = $precio * $qty - $desc;
        $computedBaseSubtotal += $baseLine;

        // accumulate impuestos per linea (detalle.impuesto expected)
        $computedTotalImpuesto += $impLinea;
        }
        }

        // facturaSubtotal: prefer stored >0 otherwise use computed base subtotal (without impuestos)
        $facturaSubtotal = (isset($factura->subtotal) && $factura->subtotal !== null && (float) $factura->subtotal >
        0.0)
        ? (float) $factura->subtotal
        : $computedBaseSubtotal;

        // If factura->total exists use it; otherwise try to compute using factura->impuesto or computedSubtotal
        // Prefer stored total when it's meaningful (> 0), otherwise compute from subtotal + impuesto
        if (isset($factura->total) && $factura->total !== null && (float) $factura->total > 0.0) {
        $facturaTotal = (float) $factura->total;
        } else {
        // prefer stored factura->impuesto when present and >0, otherwise use computed total impuestos from detalles
        $impuestoFromFactura = (isset($factura->impuesto) && $factura->impuesto !== null && (float) $factura->impuesto >
        0.0)
        ? (float) $factura->impuesto
        : $computedTotalImpuesto;
        $facturaTotal = $facturaSubtotal + $impuestoFromFactura;
        }

        // Determine impuesto: prefer explicit factura->impuesto, otherwise subtotal difference
        // Determine impuesto: prefer explicit factura->impuesto when > 0,
        // otherwise prefer computedTotalImpuesto (sum of detalle.impuesto) when >0,
        // otherwise apply business rule: impuesto = 15% del subtotal.
        if (isset($factura->impuesto) && $factura->impuesto !== null && (float) $factura->impuesto > 0.0) {
        $impuesto = (float) $factura->impuesto;
        } elseif ($computedTotalImpuesto > 0) {
        $impuesto = $computedTotalImpuesto;
        } else {
        // default tax rate: 15% (same policy used in client-side JS)
        $impuesto = round($facturaSubtotal * 0.15, 2);
        }

        // Recompute total to ensure consistency
        $facturaTotal = round($facturaSubtotal + $impuesto, 2);
        @endphp
        <div class="bloque-cliente">
            @php
            $ag = optional($factura->cliente->agencias->first());
            $agDireccion = optional($ag->direccion);
            // telefono / correo fallback from contactos
            $telefono_fallback = $cliente_telefono;
            $correo_fallback = $cliente_correo;
            if (empty($telefono_fallback) || empty($correo_fallback)) {
            $contactos = $factura->cliente->contactos ?? collect();
            foreach ($contactos as $c) {
            $tipo = strtolower(trim($c->tipo_contacto ?? ''));
            $valor = $c->valor_contacto ?? '';
            if (empty($telefono_fallback) && in_array($tipo, ['telefono','tel','phone','movil','mobile'])) {
            $telefono_fallback = $valor;
            }
            if (empty($correo_fallback) && in_array($tipo, ['email','correo','mail'])) {
            $correo_fallback = $valor;
            }
            }
            }

            // Address lines: prefer computed cliente_direccion, else build from agencia direccion
            $addr_cp = '';
            if (!empty($cliente_direccion)) {
            $addr_line1 = $cliente_direccion;
            $addr_colonia = '';
            $addr_city = '';
            } elseif ($agDireccion) {
            $addr_line1 = trim(($agDireccion->calle ?? '') . ' ' . ($agDireccion->numero ?? '')) ?:
            ($agDireccion->direccion_completa ?? '');
            $addr_colonia = $agDireccion->colonia ?? '';
            $addr_cp = $agDireccion->codigo_postal ?? '';
            $addr_city = optional($agDireccion->ciudad)->nombre_ciudad ?? '';
            $addr_depto = optional(optional($agDireccion->ciudad)->departamento)->nombre_departamento ?? '';
            } else {
            $addr_line1 = '';
            $addr_colonia = '';
            $addr_city = '';
            $addr_depto = '';
            }
            @endphp
            <div class="col">
                <p><strong>Facturar a:</strong> {{ $cliente_nombre }}</p>
                <p><strong>Dirección:</strong> {{ $addr_line1 ?: '—' }}</p>
                @if(!empty($addr_colonia) || !empty($addr_cp))
                <p>Col. {{ $addr_colonia }}{{ $addr_cp ? ', CP ' . $addr_cp : '' }}</p>
                @endif
                @if(!empty($addr_city) || !empty($addr_depto))
                <p>{{ $addr_city }}{{ $addr_depto ? ', ' . $addr_depto : '' }}</p>
                @endif
            </div>
            <div class="col">
                <p><strong>Teléfono:</strong> {{ $telefono_fallback ?: '—' }}</p>
                <p><strong>OC#:</strong> {{ $factura->oc ?? '(N/D)' }}</p>
                <p><strong>Correo electrónico:</strong> {{ $correo_fallback ?: '—' }}</p>
                <p><strong>Contacto:</strong>
                    @php
                    // Preferir nombre proveniente de cliente (empresa.contacto o persona.primer_nombre)
                    $contactoNombre = $cliente_contacto;
                    if (empty($contactoNombre)) {
                    $contactos = $factura->cliente->contactos ?? collect();
                    // Buscar contacto cuyo tipo explícitamente indique nombre/representante
                    $preferKeys = ['nombre','contacto','representante','contacto_persona','contacto_nombre'];
                    foreach ($contactos as $c) {
                    $tipo = strtolower(trim($c->tipo_contacto ?? ''));
                    $valor = trim((string) ($c->valor_contacto ?? ''));
                    if (in_array($tipo, $preferKeys) && $valor !== '') {
                    $contactoNombre = $valor;
                    break;
                    }
                    }
                    // Si no hay tipo explícito, intentar elegir un valor que no parezca teléfono/email (contenga
                    // letras)
                    if (empty($contactoNombre)) {
                    foreach ($contactos as $c) {
                    $valor = trim((string) ($c->valor_contacto ?? ''));
                    if ($valor !== '' && preg_match('/[A-Za-zÁÉÍÓÚáéíóúÑñ]/', $valor)) {
                    $contactoNombre = $valor;
                    break;
                    }
                    }
                    }
                    }
                    echo $contactoNombre ?: '—';
                    @endphp
                </p>
            </div>
            <div class="col letras">
                <p><strong>Cantidad en Letras</strong><br>{{ $factura->total_letras ?? '—' }}</p>
            </div>
        </div>

        <!-- TABLA -->
        <table class="detalle">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>DESCRIPCIÓN</th>
                    <th>HORAS</th>
                    <th>TARIFA FIJA</th>
                    <th>DESCUENTO</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detalles as $det)
                <tr>
                    <td>{{ optional($det->fecha_servicio)->format('d/m/Y') ?? ($det->fecha_servicio ?? '') }}</td>
                    <td>{{ $det->descripcion ?? ($det->servicio->nombre ?? 'Descripción') }}</td>
                    <td>{{ $det->horas ?? ($det->cantidad ?? '1') }}</td>
                    <td>{{ number_format((float) ($det->precio_unitario ?? 0), 2, '.', ',') }}</td>
                    <td>{{ number_format((float) ($det->descuento ?? 0), 2, '.', ',') }}</td>
                    <td>{{ number_format((float) ($det->total_linea ?? 0), 2, '.', ',') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay líneas en esta factura</td>
                </tr>
                @endforelse
                <tr style="height: 300px;">
                    <td colspan="6"></td>
                </tr>
            </tbody>
        </table>

        <!-- TOTALES -->
        <div class="totales">
            <p><strong>Subtotal de la factura</strong>
                <span>{{ number_format((float) ($facturaSubtotal ?? 0), 2, '.', ',') }} US$</span>
            </p>
            <p><strong>Importe del Impuesto</strong>
                <span>{{ number_format((float) ($impuesto ?? 0), 2, '.', ',') }} US$</span>
            </p>
            <p><strong>Total</strong>
                <span class="total">{{ number_format((float) ($facturaTotal ?? 0), 2, '.', ',') }} US$</span>
            </p>
        </div>

    </div>

    <!-- CSS -->
    <style>
    @page {
        size: letter;
        margin: 20mm;
    }

    body {
        font-family: Arial, sans-serif;
        background: #f1f1f1;
        padding: 30px 0;
    }

    .factura {
        width: 820px;
        margin: auto;
        background: white;
        padding: 28px 34px;
        min-height: 1100px;
        /* taller printable area to match original elongated layout */
        box-sizing: border-box;
    }

    .encabezado-completo {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0;
    }

    .bloque-encabezado {
        width: 65%;
        background: #4a4a4a;
        color: white;
        padding: 20px;
        font-size: 13px;
    }

    .bloque-encabezado h1 {
        margin: 5px 0 10px;
    }

    .bloque-encabezado .titulo {
        font-size: 13px;
        text-transform: uppercase;
        opacity: 0.85;
    }

    .bloque-factura {
        width: 34%;
        background: #e30613;
        color: white;
        padding: 20px;
        font-size: 13px;
    }

    .bloque-factura h3 {
        margin: 0 0 8px;
        font-size: 16px;
    }

    .bloque-cliente {
        background: #f9f9f9;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        font-size: 14px;
    }

    .bloque-cliente .col {
        width: 30%;
    }

    .bloque-cliente .letras {
        width: 35%;
        text-align: right;
    }

    .detalle {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 0;
    }

    .detalle thead {
        background: #e30613;
        color: white;
    }

    .detalle th,
    .detalle td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .totales {
        width: 100%;
        padding: 30px 0;
        text-align: right;
        font-size: 14px;
    }

    .totales p {
        margin: 5px 30px 5px 0;
    }

    .totales span {
        display: inline-block;
        min-width: 120px;
        text-align: right;
    }

    .totales .total {
        font-weight: bold;
        font-size: 15px;
        border-top: 1px solid black;
        padding-top: 5px;
    }

    .encabezado-completo {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .bloque-encabezado {
        width: 65%;
        background: #4a4a4a;
        color: white;
        padding: 20px;
        font-size: 13px;
        box-sizing: border-box;
    }

    .bloque-factura {
        width: 35%;
        background: #e30613;
        color: white;
        padding: 20px;
        font-size: 13px;
        box-sizing: border-box;
    }

    .fila-superior {
        margin-bottom: 10px;
    }

    .fila-superior h1 {
        margin: 0 0 10px;
    }

    .titulo {
        font-size: 13px;
        text-transform: uppercase;
        opacity: 0.85;
        margin: 0;
    }

    .fila-inferior {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .fila-inferior .col {
        width: 32%;
    }
    </style>
</body>

</html>