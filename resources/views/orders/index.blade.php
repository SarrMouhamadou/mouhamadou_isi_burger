@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Liste des Commandes</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <a href="{{ route('orders.create') }}" class="btn btn-primary mb-3">Créer une Commande</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Email</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_email }}</td>
                    <td>{{ number_format($order->total, 2, ',', '.') }} €</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
