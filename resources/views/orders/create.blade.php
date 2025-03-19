@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Créer une Commande</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="customer_name" class="form-label">Nom du Client</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
        </div>
        <div class="mb-3">
            <label for="customer_email" class="form-label">Email du Client</label>
            <input type="email" name="customer_email" id="customer_email" class="form-control" value="{{ old('customer_email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Burgers</label>
            @foreach ($burgers as $burger)
                <div class="input-group mb-2">
                    <div class="input-group-text">
                        <input type="checkbox" name="burgers[{{ $burger->id }}][burger_id]" value="{{ $burger->id }}">
                    </div>
                    <input type="text" class="form-control" value="{{ $burger->name }}" disabled>
                    <input type="number" name="burgers[{{ $burger->id }}][quantity]" class="form-control" min="1" max="{{ $burger->stock }}" placeholder="Quantité">
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
@endsection
