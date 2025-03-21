<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du burger - {{ $burger->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B4A;
            --primary-light: #FFECE8;
            --text-dark: #333333;
            --text-light: #757575;
            --bg-light: #F9F9F9;
        }

        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        .burger-image {
            max-height: 300px;
            border-radius: 15px;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">{{ $burger->name }}</h1>
        <div class="row">
            <div class="col-md-6">
                <img src="{{ $burger->image ? asset('storage/' . $burger->image) : asset('images/default-burger.png') }}"
                     alt="{{ $burger->name }}" class="img-fluid burger-image">
            </div>
            <div class="col-md-6">
                <h3>Prix : {{ number_format($burger->price, 2) }} FCFA</h3>
                <p><strong>Catégorie :</strong> {{ $burger->category ?? 'Non spécifiée' }}</p>
                <p><strong>Stock :</strong> {{ $burger->stock }} unités</p>
                <p><strong>Description :</strong> {{ $burger->description ?? 'Aucune description disponible.' }}</p>
                <form action="{{ route('cart.add', $burger->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                </form>
                <a href="{{ route('home') }}" class="btn btn-secondary mt-2">Retour au catalogue</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
