<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Gestion des Burgers</title>
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
                <h2>Gestion des Burgers</h2>
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

            <!-- Messages de succès ou d'erreur -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bouton pour ajouter un burger -->
            <div class="mb-4">
                <a href="{{ route('burgers.create') }}" class="btn btn-primary">Ajouter un Burger</a>
            </div>

            <!-- Tableau des burgers -->
            <div class="card">
                <div class="card-header">
                    <h4>Liste des Burgers</h4>
                </div>
                <div class="card-body">
                    @if ($burgers->isEmpty())
                        <p>Aucun burger disponible pour le moment.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Catégorie</th>
                                    <th>Archivé</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($burgers as $burger)
                                    <tr>
                                        <td>{{ $burger->id }}</td>
                                        <td>
                                            @if ($burger->image)
                                                <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                            @else
                                                <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                            @endif
                                        </td>
                                        <td>{{ $burger->name }}</td>
                                        <td>{{ number_format($burger->price, 2) }} FCFA</td>
                                        <td>{{ $burger->stock }}</td>
                                        <td>{{ $burger->category ?? 'N/A' }}</td>
                                        <td>{{ $burger->archived ? 'Oui' : 'Non' }}</td>
                                        <td>
                                            <a href="{{ route('burgers.edit', $burger) }}" class="btn btn-sm btn-warning">Modifier</a>
                                            <form action="{{ route('burgers.destroy', $burger) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce burger ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $burgers->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
