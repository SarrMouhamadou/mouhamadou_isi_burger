<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Gestion des Commandes</title>
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
        .order-table th, .order-table td { vertical-align: middle; }
        .order-table img { width: 40px; height: 40px; object-fit: cover; border-radius: 5px; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.9rem; }
        .status-en-attente { background-color: #ffcc00; color: white; }
        .status-en-préparation { background-color: #007bff; color: white; }
        .status-prête { background-color: #28a745; color: white; }
        .status-payée { background-color: #17a2b8; color: white; }
        .status-annulée { background-color: #dc3545; color: white; }
    </style>
</head>
<body class="h-full">
    <div class="d-flex">
        <!-- Sidebar pour Gestionnaire -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-center">ISI Burger - Gestionnaire</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('gestionnaire.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('orders.index') }}">Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payments.index') }}">Paiements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="statistiques">Statistiques</a>
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
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestion des Commandes</h2>
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

            <!-- Liste des commandes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Liste des Commandes</h4>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">Créer une Commande</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <table class="table order-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Burger</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Client</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        @if ($order->burgers->first() && $order->burgers->first()->image)
                                            <img src="{{ asset('storage/' . $order->burgers->first()->image) }}" alt="{{ $order->burgers->first()->name }}">
                                        @else
                                            <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut">
                                        @endif
                                    </td>
                                    <td>{{ $order->burgers->first() ? $order->burgers->first()->name : 'N/A' }}</td>
                                    <td>{{ $order->burgers->sum('pivot.quantity') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} FCFA</td>
                                    <td>{{ $order->user ? $order->user->name : $order->customer_name }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('orders.show', $order->id) }}">Voir détails</a>
                                                </li>
                                                @if ($order->status != 'Payée' && $order->status != 'Annulée')
                                                    <li>
                                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="En attente">
                                                            <button type="submit" class="dropdown-item">En attente</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="En préparation">
                                                            <button type="submit" class="dropdown-item">En préparation</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="Prête">
                                                            <button type="submit" class="dropdown-item">Prête</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="Payée">
                                                            <button type="submit" class="dropdown-item">Payée</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('orders.update', $order->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="Annulée">
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">Annuler</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
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
