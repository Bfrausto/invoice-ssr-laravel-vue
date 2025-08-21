<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $invoice->folio }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; }
        .details, .items-table { width: 100%; margin-top: 25px; border-collapse: collapse; }
        .details td, .items-table td, .items-table th { padding: 8px; border: 1px solid #ddd; }
        .items-table th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .totals { float: right; width: 280px; margin-top: 20px; }
        .totals td { padding: 5px; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="header">FACTURA</h1>
    <table class="details">
        <tr>
            <td>
                <strong>Emisor:</strong><br>
                {{ $invoice->company->name }}<br>
                {{ $invoice->company->address }}<br>
                {{ $invoice->company->email }}
            </td>
            <td>
                <strong>Receptor:</strong><br>
                {{ $invoice->client->name }}<br>
                {{ $invoice->client->address }}<br>
                {{ $invoice->client->email }}
            </td>
            <td>
                <strong>Folio:</strong> #{{ $invoice->folio }}<br>
                <strong>Fecha de Emisión:</strong> {{ $invoice->issue_date }}<br>
                <strong>Fecha de Vencimiento:</strong> {{ $invoice->due_date }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
        <tr>
            <th>Descripción</th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">Precio Unit.</th>
            <th class="text-right">Desc. (%)</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->discount, 2) }}%</td>
                <td class="text-right">${{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="text-right"><strong>Subtotal (antes de descuentos):</strong></td>
            <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        @if($invoice->global_discount > 0)
            <tr>
                @php
                    $globalDiscountAmount = $invoice->subtotal * ($invoice->global_discount / 100);
                @endphp
                <td class="text-right">Descuento Global ({{ number_format($invoice->global_discount, 2) }}%):</td>
                <td class="text-right">-${{ number_format($globalDiscountAmount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td class="text-right"><strong>Subtotal (con descuentos):</strong></td>
            <td class="text-right"><strong>${{ number_format($invoice->subtotal - ($invoice->subtotal * $invoice->global_discount / 100), 2) }}</strong></td>
        </tr>
        @if($invoice->tax)
            <tr>
                <td class="text-right">Impuestos ({{ $invoice->tax->name }} {{ $invoice->tax->rate }}%):</td>
                <td class="text-right">${{ number_format($invoice->total_taxes, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td class="text-right font-bold" style="font-size: 14px;">Total:</td>
            <td class="text-right font-bold" style="font-size: 14px;">${{ number_format($invoice->total, 2) }}</td>
        </tr>
    </table>
</div>
</body>
</html>
