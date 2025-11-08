<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura Hardlan</title>
</head>

<body>
    @php
    $cliente_nombre = $datosCliente['cliente_nombre'];
    $cliente_direccion = $datosCliente['cliente_direccion'];
    $cliente_telefono = $datosCliente['cliente_telefono'];
    $cliente_correo = $datosCliente['cliente_correo'];
    $cliente_contacto = $datosCliente['cliente_contacto'];
    @endphp

    <div class="factura">
        <div class="print-button-container">
            <button onclick="window.print()" class="print-btn">
                📄 Imprimir Factura
            </button>
        </div>
        
        <div class="encabezado-completo">
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

            <div class="bloque-factura">
                <h3>{{ $factura->numero ?? '---' }}</h3>
                <p><strong>FECHA:</strong>
                    {{ $factura->fecha ? \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') : ($factura->fecha ?? '') }}
                </p>
                <p><strong>Rango Autorizado:</strong><br>{{ $factura->cai->rango_inicio ?? '—' }}
                    al<br>{{ $factura->cai->rango_fin ?? '—' }}</p>

                @php
                @endphp
                <p><strong>Fecha límite de emisión:</strong><br>
                    {{ $fechaLimite ? (\Carbon\Carbon::parse($fechaLimite)->format('d/m/Y')) : '—' }}
                </p>
            </div>
        </div>


        @php
        $facturaSubtotal = $totales['facturaSubtotal'];
        $impuesto = $totales['impuesto'];
        $facturaTotal = $totales['facturaTotal'];
        @endphp
        <div class="bloque-cliente">
            @php
            $telefono_fallback = $datosContacto['telefono_fallback'];
            $correo_fallback = $datosContacto['correo_fallback'];
            $addr_line1 = $datosContacto['addr_line1'];
            $addr_colonia = $datosContacto['addr_colonia'];
            $addr_cp = $datosContacto['addr_cp'];
            $addr_city = $datosContacto['addr_city'];
            $addr_depto = $datosContacto['addr_depto'];
            $contactoNombre = $datosContacto['contactoNombre'];
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
                    {{ $contactoNombre ?: '—' }}
                </p>
            </div>
            <div class="col letras">
                <p><strong>Cantidad en Letras</strong><br>{{ $factura->total_letras ?? '—' }}</p>
            </div>
        </div>

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
                    <td>L {{ number_format((float) ($det->precio_unitario ?? 0), 2, '.', ',') }}</td>
                    <td>L {{ number_format((float) ($det->descuento ?? 0), 2, '.', ',') }}</td>
                    <td>L {{ number_format((float) ($det->total_linea ?? 0), 2, '.', ',') }}</td>
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

        <div class="totales">
            <p><strong>Subtotal de la factura</strong>
                <span>L {{ number_format((float) ($facturaSubtotal ?? 0), 2, '.', ',') }}</span>
            </p>
            <p><strong>Importe del Impuesto</strong>
                <span>L {{ number_format((float) ($impuesto ?? 0), 2, '.', ',') }}</span>
            </p>
            <p><strong>Total</strong>
                <span class="total">L {{ number_format((float) ($facturaTotal ?? 0), 2, '.', ',') }}</span>
            </p>
        </div>

    </div>

    <style>
    @page {
        size: letter;
        margin: 20mm;
    }

    @media print {
        .print-button-container {
            display: none !important;
        }
    }

    body {
        font-family: Arial, sans-serif;
        background: #f1f1f1;
        padding: 30px 0;
    }

    .print-button-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .print-btn {
        background-color: #1e40af;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .print-btn:hover {
        background-color: #1d4ed8;
    }

    .factura {
        width: 820px;
        margin: auto;
        background: white;
        padding: 28px 34px;
        min-height: 1100px;
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