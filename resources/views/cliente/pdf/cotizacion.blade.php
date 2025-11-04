<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $codigo }}</title>
    <style>
    body {
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #111;
    }

    .container {
        width: 100%;
    }

    .row {
        display: flex;
        width: 100%;
    }

    .col {
        flex: 1;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .mb-2 {
        margin-bottom: 8px;
    }

    .mb-3 {
        margin-bottom: 12px;
    }

    .mb-4 {
        margin-bottom: 16px;
    }

    .mt-2 {
        margin-top: 8px;
    }

    .mt-3 {
        margin-top: 12px;
    }

    .muted {
        color: #666;
    }

    h1,
    h2,
    h3 {
        margin: 0;
        padding: 0;
    }

    h1 {
        font-size: 20px;
    }

    h2 {
        font-size: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 6px;
    }

    thead th {
        background: #f2f2f2;
    }

    .no-border td,
    .no-border th {
        border: none;
    }

    .totals td {
        border: none;
        padding: 4px 6px;
    }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
    }

    .badge-borrador {
        background: #FDE68A;
        color: #92400E;
    }

    .badge-aprobada {
        background: #A7F3D0;
        color: #065F46;
    }

    .badge-rechazada {
        background: #FCA5A5;
        color: #7F1D1D;
    }

    .badge-vencida {
        background: #BFDBFE;
        color: #1E3A8A;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1>Cotización</h1>
                <div class="muted">Código: <strong>{{ $codigo }}</strong></div>
                <div class="muted">Fecha:
                    <strong>{{ optional($cotizacion->fecha_cotizacion)->format('Y-m-d') }}</strong></div>
                <div class="muted">Válido hasta:
                    <strong>{{ optional($cotizacion->valido_hasta)->format('Y-m-d') }}</strong></div>
                @php
                $estadoNombre = optional($cotizacion->estado)->nombre;
                $estadoCodigo = optional($cotizacion->estado)->codigo;
                $badgeClass = 'badge';
                $code = $estadoCodigo ? strtoupper($estadoCodigo) : '';
                if ($code === 'BRD') $badgeClass .= ' badge-borrador';
                elseif ($code === 'APB') $badgeClass .= ' badge-aprobada';
                elseif ($code === 'REC') $badgeClass .= ' badge-rechazada';
                elseif ($code === 'VEN') $badgeClass .= ' badge-vencida';
                @endphp
                @if($estadoNombre)
                <div class="mt-2">
                    <span class="{{ $badgeClass }}">{{ $estadoNombre }}</span>
                </div>
                @endif
            </div>
            <div class="col text-right">
                @php
                $cliente = $cotizacion->cliente;
                $esEmpresa = $cliente && $cliente->relationLoaded('empresa') && $cliente->empresa;
                $nombreCliente = $esEmpresa
                ? ($cliente->empresa->nombre_comercial ?: $cliente->empresa->razon_social)
                : (optional($cliente->personas->first())->primer_nombre . ' ' .
                optional($cliente->personas->first())->primer_apellido);
                @endphp
                <h2>Cliente</h2>
                <div><strong>{{ trim($nombreCliente) ?: 'N/D' }}</strong></div>
                @if($esEmpresa && $cliente->empresa && $cliente->empresa->rtn)
                <div class="muted">RTN: {{ $cliente->empresa->rtn }}</div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 52%">Descripción</th>
                    <th style="width: 12%" class="text-right">Precio Unit.</th>
                    <th style="width: 12%" class="text-right">Cantidad</th>
                    <th style="width: 12%" class="text-right">Impuesto</th>
                    <th style="width: 12%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $it)
                <tr>
                    <td>{{ $it->descripcion }}</td>
                    <td class="text-right">L. {{ number_format((float)$it->precio_unitario, 2) }}</td>
                    <td class="text-right">{{ number_format((float)$it->cantidad, 2) }}</td>
                    <td class="text-right">L. {{ number_format((float)$it->impuesto, 2) }}</td>
                    <td class="text-right">L. {{ number_format((float)$it->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center muted">No hay items en esta cotización.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @php
        $subtotal = (float)($cotizacion->imponible ?? 0);
        $totalImp = (float)($cotizacion->total_impuesto ?? $cotizacion->impuesto ?? 0);
        $otros = (float)($cotizacion->otros_cargos ?? 0);
        $otrosImp = (float)($cotizacion->impuesto_otros ?? 0);
        $total = (float)($cotizacion->total ?? ($subtotal + $totalImp + $otros + $otrosImp));
        @endphp
        <table class="no-border" style="margin-top: 14px;">
            <tr class="totals">
                <td class="col"></td>
                <td class="col"></td>
                <td class="col"></td>
                <td class="col text-right">Subtotal:</td>
                <td class="col text-right">L. {{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr class="totals">
                <td class="col"></td>
                <td class="col"></td>
                <td class="col"></td>
                <td class="col text-right">Impuestos:</td>
                <td class="col text-right">L. {{ number_format($totalImp, 2) }}</td>
            </tr>
            <tr class="totals">
                <td class="col"></td>
                <td class="col"></td>
                <td class="col"></td>
                <td class="col text-right">Otros cargos:</td>
                <td class="col text-right">L. {{ number_format($otros, 2) }}</td>
            </tr>
            <tr class="totals">
                <td class="col"></td>
                <td class="col"></td>
                <td class="col"></td>
                <td class="col text-right">Impuesto otros:</td>
                <td class="col text-right">L. {{ number_format($otrosImp, 2) }}</td>
            </tr>
            <tr class="totals" style="font-weight: bold;">
                <td class="col"></td>
                <td class="col"></td>
                <td class="col"></td>
                <td class="col text-right">Total:</td>
                <td class="col text-right">L. {{ number_format($total, 2) }}</td>
            </tr>
        </table>

        @if($cotizacion->anticipo_requerido)
        <div class="mt-3"><strong>Anticipo requerido:</strong> L.
            {{ number_format((float)$cotizacion->anticipo_requerido, 2) }}</div>
        @endif

        <div class="mt-3 muted">
            Este documento es una propuesta comercial. Los precios y condiciones son válidos hasta la fecha indicada.
            Para aceptar la cotización, por favor comuníquese con soporte.
        </div>
    </div>
</body>

</html>