@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Modifier Commande #{{ $order->id }}</h1>

    <form action="{{ route('orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="status" class="form-label">Statut</label>
            <select name="status" id="status" class="form-control" required>
                <option value="en_attente" {{ $order->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="en_preparation" {{ $order->status == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                <option value="prete" {{ $order->status == 'prete' ? 'selected' : '' }}>Prête</option>
                <option value="payee" {{ $order->status == 'payee' ? 'selected' : '' }}>Payée</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Retour</a>
@endsection
