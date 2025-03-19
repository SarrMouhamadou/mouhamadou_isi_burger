@extends('layouts.app')

@section('content')
    <h1 class="mt-4">Payments</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Record New Payment</a>

    @if ($payments->isEmpty())
        <p>No payments found.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->order_id }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->status }}</td>
                        <td>{{ $payment->payment_date->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <!-- Ajoute des actions si nécessaire -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
