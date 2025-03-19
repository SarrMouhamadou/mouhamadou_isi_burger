@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Commande #{{ $order->id }}</h1>

    <p><strong>Client :</strong> {{ $order->customer_name }}</p>
    <p><strong>Email :</strong> {{ $order->customer_email }}</p>
    <p><strong>Total :</strong> {{ number_format($order->total, 2, ',', '.') }} €</p>
    <p><strong>Statut :</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>

    <h4 class="mt-4">Articles</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Burger</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Sous-Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->burgers as $burger)
                <tr>
                    <td>{{ $burger->name }}</td>
                    <td>{{ $burger->pivot->quantity }}</td>
                    <td>{{ number_format($burger->pivot->unit_price, 2, ',', '.') }} €</td>
                    <td>{{ number_format($burger->pivot->unit_price * $burger->pivot->quantity, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Retour</a>
@endsection
