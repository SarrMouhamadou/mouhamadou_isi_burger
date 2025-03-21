<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Détails du Burger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f6f9; }
        .sidebar { background-color: #f97316; color: white; min-height: 100vh; }
        .sidebar a { color: white; }
        .sidebar a:hover { background-color: #e65c00; }
        .card { border-radius: 10px; border: none; }
        .nav-link.active { background-color: #e65c00; }
    </style>
</head>
<body class="h-full">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-center">ISI Burger - Gestionnaire</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('gestionnaire.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('gestionnaire.orders.index') }}">Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payments.index') }}">Paiements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="statistiques">Statistiques</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('burgers.index') }}">Burgers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Déconnexion
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>

        <!-- Contenu Principal -->
        <div class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Détails du Burger</h2>
                <div class="dropdown">
                    <a class="dropdown-toggle text-dark" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                Déconnexion
                            </a>
                            <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Détails du burger -->
            <div class="card">
                <div class="card-header">
                    <h4>{{ $burger->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if ($burger->image)
                                <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" class="img-fluid rounded" style="max-width: 100%;">
                            @else
                                <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut" class="img-fluid rounded" style="max-width: 100%;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <p><strong>Prix :</strong> {{ number_format($burger->price, 2) }} FCFA</p>
                            <p><strong>Stock :</strong> {{ $burger->stock }}</p>
                            <p><strong>Catégorie :</strong> {{ $burger->category ?? 'N/A' }}</p>
                            <p><strong>Description :</strong> {{ $burger->description ?? 'Aucune description disponible.' }}</p>
                            <p><strong>Archivé :</strong> {{ $burger->archived ? 'Oui' : 'Non' }}</p>
                            <a href="{{ route('burgers.edit', $burger) }}" class="btn btn-warning">Modifier</a>
                            <a href="{{ route('burgers.index') }}" class="btn btn-secondary">Retour</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
