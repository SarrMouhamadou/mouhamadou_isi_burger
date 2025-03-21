<!DOCTYPE html>
<html>
<head>
    <title>Facture Commande #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .invoice-box table { width: 100%; line-height: 1.5; border-collapse: collapse; }
        .invoice-box table th, .invoice-box table td { padding: 10px; }
        .invoice-box table th { background-color: #f97316; color: white; }
        .invoice-box table td { border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h1>Facture Commande #{{ $order->id }}</h1>
        <p><strong>Client :</strong> {{ $order->user->name }}</p>
        <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y') }}</p>

        <table>
            <thead>
                <tr>
                    <th>Burger</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->burgers as $burger)
                    <tr>
                        <td>{{ $burger->name }}</td>
                        <td>{{ $burger->pivot->quantity }}</td>
                        <td>{{ number_format($burger->price, 2) }} FCFA</td>
                        <td>{{ number_format($burger->price * $burger->pivot->quantity, 2) }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td>{{ number_format($order->total_amount, 2) }} FCFA</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
