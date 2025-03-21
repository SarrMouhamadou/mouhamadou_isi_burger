<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Détails de la Commande #{{ $order->id }}</title>
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
                <h2>Détails de la Commande #{{ $order->id }}</h2>
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

            <!-- Détails de la commande -->
            <div class="card">
                <div class="card-header">
                    <h4>Commande #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p><strong>Client :</strong> {{ $order->user ? $order->user->name : $order->customer_name }}</p>
                    <p><strong>Email :</strong> {{ $order->customer_email }}</p>
                    <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Statut :</strong>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">
                            {{ $order->status }}
                        </span>
                    </p>

                    <h5 class="mt-4">Burgers commandés</h5>
                    <table class="table order-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Nom</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->burgers as $burger)
                                <tr>
                                    <td>
                                        @if ($burger->image)
                                            <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}">
                                        @else
                                            <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut">
                                        @endif
                                    </td>
                                    <td>{{ $burger->name }}</td>
                                    <td>{{ $burger->pivot->quantity }}</td>
                                    <td>{{ number_format($burger->pivot->unit_price, 2) }} FCFA</td>
                                    <td>{{ number_format($burger->pivot->quantity * $burger->pivot->unit_price, 2) }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Total</strong></td>
                                <td>{{ number_format($order->total_amount, 2) }} FCFA</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Actions -->
                    @if ($order->status != 'Payée' && $order->status != 'Annulée')
                        <div class="mt-4">
                            <h5>Modifier le statut</h5>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Changer le statut
                                </button>
                                <ul class="dropdown-menu">
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
                                </ul>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-4">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
