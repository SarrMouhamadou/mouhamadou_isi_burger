@extends('layouts.app')

@section('content')
    <h1 class="mt-4">Record a New Payment</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('payments.store') }}" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="order_id" class="form-label">Order</label>
            <select name="order_id" id="order_id" class="form-select" required>
                <option value="">Select an order</option>
                @foreach ($orders as $order)
                    <option value="{{ $order->id }}">Order #{{ $order->id }} - {{ $order->customer_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="" disabled selected>Select a status</option>
                <option value="en_attente">En attente</option>
                <option value="réussi">Réussi</option>
                <option value="échoué">Echoué</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Record Payment</button>
    </form>

    <a href="{{ route('payments.index') }}" class="btn btn-secondary mt-3">Back to Payments</a>
@endsection
