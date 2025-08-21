<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $invoice->folio }}</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; }
        .details, .items-table { width: 100%; margin-top: 20px; }
        .details td, .items-table td, .items-table th { padding: 8px; border: 1px solid #ddd; }
        .items-table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
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

    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">${{ number_format($item->price, 2) }}</td>
                <td class="text-right">${{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="details text-right" style="margin-top: 20px;">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td>${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Impuestos ({{ $invoice->tax->name }} {{ $invoice->tax->rate }}%):</strong></td>
            <td>${{ number_format($invoice->total_taxes, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total:</strong></td>
            <td><strong>${{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</strong></td>
        </tr>
    </table>
</div>
</body>
</html>
