<!DOCTYPE html>
<html lang="es" x-data="cotizacionDetalle()" x-init="init()">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cotización - IT SUPPORT HARDLAN</title>
    <style>
        body {
            font-family: "Roboto", sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            color: #333;
            font-size: 14px;
        }

        .container {
            width: 21cm;
            min-height: 29.7cm;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .logo-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-right: 0;
            margin-bottom: 10px;
        }

        .app-logo {
            width: 150px;
            height: 100px;
            object-fit: contain;
            margin-right: 0;
            margin-bottom: 10px;
        }

        .company-info p {
            margin: 0;
            line-height: 1.4;
            font-size: 13px;
        }

        .quotation-header {
            text-align: right;
        }

        .quotation-header h1 {
            color: #00008b;
            margin: 0 0 10px 0;
            font-size: 28px;
            border-bottom: 2px solid #00008b;
            padding-bottom: 5px;
        }

        .header-details {
            display: block;
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-row span:first-child {
            font-weight: bold;
            color: #555;
            flex-shrink: 0;
            margin-right: 15px;
        }

        .detail-row .value {
            background-color: #fff;
            padding: 2px 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            display: inline-block;
            min-width: 100px;
            text-align: center;
            flex-grow: 1;
        }

        .detail-row .value.date {
            background-color: #fff;
        }

        .client-section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
            width: 60%;
        }

        .client-section h2 {
            background-color: #00008b;
            color: #fff;
            padding: 8px 15px;
            margin: -15px -15px 15px -15px;
            font-size: 16px;
            text-transform: uppercase;
        }

        .client-section p {
            margin: 5px 0;
            font-size: 14px;
        }

        .items-section {
            margin-bottom: 25px;
        }

        .items-section table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-section th,
        .items-section td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }

        .items-section th {
            background-color: #00008b;
            color: #fff;
            text-transform: uppercase;
            font-weight: normal;
        }

        .items-section td {
            vertical-align: top;
        }

        .items-section .description {
            width: 50%;
        }

        .items-section .unit-price,
        .items-section .quantity,
        .items-section .taxes,
        .items-section .total {
            text-align: right;
            width: 12.5%;
        }

        .items-section .total {
            font-weight: bold;
        }

        .bottom-sections-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary {
            width: 300px;
            border: 1px solid #000;
            flex-shrink: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row .amount {
            font-weight: normal;
            text-align: right;
            min-width: 80px;
        }

        .total-row {
            background-color: #00008b;
            color: #fff;
            font-weight: bold;
            padding: 8px 10px;
        }

        .total-row .amount {
            font-weight: bold;
            color: #fff;
        }

        .terms-section {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .terms-section h2 {
            background-color: #00008b;
            color: #fff;
            padding: 8px 15px;
            margin: -15px -15px 15px -15px;
            font-size: 16px;
            text-transform: uppercase;
        }

        .terms-section ol {
            padding-left: 20px;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .terms-section ol li {
            margin-bottom: 5px;
            font-size: 13px;
        }

        .terms-section p {
            margin: 5px 0;
            font-size: 13px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 20px;
            padding-bottom: 5px;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #555;
        }

        footer .thanks {
            font-weight: bold;
            font-size: 14px;
            color: #00008b;
            margin-top: 10px;
        }

        .print-button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-button {
            background-color: #00008b;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                page-break-inside: avoid;
                width: 100%;
                min-height: auto;
                box-shadow: none;
            }

            .items-section table {
                page-break-inside: avoid;
            }

            .terms-section {
                page-break-inside: avoid;
            }

            .summary {
                page-break-inside: avoid;
            }

            .print-button {
                display: none;
            }
        }
    </style>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap"
        rel="stylesheet" />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body>
    <div class="container">
        <header>
            <div class="logo-section">
                <!-- Using the provided image URL for the logo -->
                <img
                    src="{{ $appLogoUrl ?? asset('images/logo.png') }}"
                    alt="IT SUPPORT HARDLAN Logo"
                    class="app-logo"
                    style="--app-logo-max: {{ ($appLogoHeight ?? 96) }}px;" />
                <div class="company-info">
                    <p>
                        Col. Centro América Oeste, Zona 4, Bloque G, Casa 17
                    </p>
                    <p>Comayagüela, M.D.C. Francisco Morazán</p>
                    <p>Teléfono: [504] 2227-0705, 9877-7244</p>
                    <p>Asesor de venta: Edwyn Lagos</p>
                </div>
            </div>
            <div class="quotation-header">
                <h1>COTIZACIÓN</h1>
                <div class="header-details">
                    <!-- Changed to display details in a column (one below another) -->
                    <div class="detail-row">
                        <span>FECHA</span>
                        <span class="value date" x-text="formatFecha(cotizacion?.fecha_cotizacion)">--</span>
                    </div>
                    <div class="detail-row">
                        <span>COTIZACIÓN #</span>
                        <span class="value" x-text="cotizacion?.id_cotizacion_pk ?? '-'">-</span>
                    </div>
                    <div class="detail-row">
                        <span>CLIENTE ID</span>
                        <span class="value" x-text="cotizacion?.id_cliente_fk ?? '-'">-</span>
                    </div>
                    <div class="detail-row">
                        <span>VALIDO HASTA</span>
                        <span class="value date" x-text="formatFecha(cotizacion?.valido_hasta)">--</span>
                    </div>
                </div>
            </div>
        </header>

        <section class="client-section">
            <h2>CLIENTE</h2>
            <p x-text="cotizacion?.cliente_nombre || (cotizacion?.cliente?.empresa?.nombre_comercial ?? cotizacion?.cliente?.empresa?.razon_social) || '-'">-</p>
            <p x-text="contactoNombre || ''"></p>
        </section>

        <section class="items-section">
            <table>
                <thead>
                    <tr>
                        <th class="description">DESCRIPCIÓN</th>
                        <th class="unit-price">PRECIO UNIT.</th>
                        <th class="quantity">CANT.</th>
                        <th class="taxes">IMPUESTOS</th>
                        <th class="total">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="it in items" :key="it.id_item_cotizacion_pk">
                        <tr>
                            <td class="description" x-text="it.descripcion"></td>
                            <td class="unit-price" x-text="fmt(it.precio_unitario)"></td>
                            <td class="quantity" x-text="fmt(it.cantidad)"></td>
                            <td class="taxes" x-text="fmt(it.impuesto)"></td>
                            <td class="total" x-text="fmt(it.total)"></td>
                        </tr>
                    </template>
                    <tr x-show="!items.length">
                        <td colspan="5" style="text-align:center; color:#666;">Sin ítems</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- New container to place terms and summary side-by-side -->
        <div class="bottom-sections-container">
            <section class="terms-section">
                <h2>TÉRMINOS Y CONDICIONES</h2>
                <ol>
                    <li>
                        Se requiere 50% de anticipo y la diferencia al
                        entregar el proyecto.
                    </li>
                    <li>
                        Por favor enviar la cotización firmada al email
                        indicado.
                    </li>
                </ol>
                <p>La aceptación del cliente (firmar a continuación):</p>
                <div class="signature-line">
                    X.
                    ________________________________________________________________
                </div>
                <p>Nombre del cliente</p>
            </section>

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="amount" x-text="money(cotizacion?.subtotal)">$ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Imponible</span>
                    <span class="amount" x-text="money(cotizacion?.imponible)">$ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Total Impuesto</span>
                    <span class="amount" x-text="money(cotizacion?.total_impuesto)">$ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Otros</span>
                    <span class="amount" x-text="money(cotizacion?.otros_cargos)">$ 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Anticipo</span>
                    <span class="amount" x-text="money(cotizacion?.anticipo_requerido)">$ 0.00</span>
                </div>
                <div class="summary-row total-row">
                    <span>TOTAL</span>
                    <span class="amount" x-text="money(cotizacion?.total)">$ 0.00</span>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <button class="print-button" onclick="window.print()" style="background-color: #00008b; color: white; border: none; padding: 5px 10px; font-size: 14px; border-radius: 5px; cursor: pointer;">
                Imprimir Cotización
            </button>
        </div>

        <footer>
            <p>
                Si usted tiene alguna pregunta sobre esta cotización, por
                favor, póngase en contacto con nosotros
            </p>
            <p>Edwyn Lagos, edw.lagos@gmail.com, [504] 9877-7244</p>
            <p class="thanks">Gracias por hacer negocios con nosotros!</p>
        </footer>
    </div>
    <script>
        function cotizacionDetalle() {
            return {
                cotizacion: null,
                items: [],
                contactoNombre: '',
                apiHeaders() {
                    const t = localStorage.getItem('authToken');
                    return {
                        'Accept': 'application/json',
                        ...(t ? {
                            'Authorization': 'Bearer ' + t
                        } : {})
                    };
                },
                async ensureToken() {
                    if (localStorage.getItem('authToken')) return true;
                    try {
                        const r = await fetch('/session/token', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (r.ok) {
                            const j = await r.json();
                            if (j?.token) {
                                localStorage.setItem('authToken', j.token);
                                return true;
                            }
                        }
                    } catch (e) {}
                    return false;
                },
                async fetchWithAuth(url) {
                    let r = await fetch(url, {
                        headers: this.apiHeaders()
                    });
                    if (r.status === 401) {
                        const ok = await this.ensureToken();
                        if (ok) {
                            r = await fetch(url, {
                                headers: this.apiHeaders()
                            });
                        }
                    }
                    return r;
                },
                idFromQuery() {
                    const u = new URL(window.location.href);
                    return u.searchParams.get('id');
                },
                fmt(n) {
                    if (n == null) return '';
                    return Number(n).toFixed(2);
                },
                money(n) {
                    if (n == null) return '$ 0.00';
                    return '$ ' + Number(n).toFixed(2);
                },
                formatFecha(s) {
                    if (!s) return '--';
                    if (s.length === 10) return new Date(s + 'T00:00:00').toLocaleDateString();
                    return new Date(s).toLocaleDateString();
                },
                async init() {
                    try {
                        const id = this.idFromQuery();
                        if (!id) return;
                        const r = await this.fetchWithAuth(`/api/cotizaciones/${id}`);
                        if (r.ok) {
                            const j = await r.json();
                            this.cotizacion = j.data || j;
                        }
                        const ri = await this.fetchWithAuth(`/api/items-cotizacion?all=1&id_cotizacion_fk=${id}`);
                        if (ri.ok) {
                            const ji = await ri.json();
                            this.items = (ji.data || ji || []);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        }
    </script>
</body>

</html>