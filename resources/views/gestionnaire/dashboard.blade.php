<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Gestionnaire Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f6f9; }
        .sidebar { background-color: #f97316; color: white; min-height: 100vh; }
        .sidebar a { color: white; }
        .sidebar a:hover { background-color: #e65c00; }
        .card { border-radius: 10px; border: none; }
        .nav-link.active { background-color: #e65c00; }
        .stat-card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .stat-card h5 { color: #f97316; font-size: 1.2rem; }
        .stat-card p { font-size: 1.5rem; font-weight: bold; margin: 0; }
        .stat-card small { color: #6c757d; }
        .chart-card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .chart-card canvas { max-height: 200px !important; width: 100% !important; height: 200px !important; } /* Forcer la hauteur */
        .order-table th, .order-table td { vertical-align: middle; }
        .order-table img { width: 40px; height: 40px; object-fit: cover; border-radius: 5px; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.9rem; }
        .status-en-attente { background-color: #ffcc00; color: white; }
        .status-en-préparation { background-color: #007bff; color: white; }
        .status-prête { background-color: #28a745; color: white; }
        .status-payée { background-color: #17a2b8; color: white; }
        .status-annulée { background-color: #dc3545; color: white; }
        .profile-section img { object-fit: cover; }
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
                    <a class="nav-link active" href="#" data-section="dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}">Commandes</a>
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
                <h2>Tableau de Bord Gestionnaire</h2>
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

            <!-- Contenu Dynamique -->
            <div id="content">
                @if (session('status') || session('success'))
                    <div class="alert alert-success">
                        {{ session('status') ?? session('success') }}
                    </div>
                @endif

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Commandes en cours (aujourd'hui)</h5>
                            <p>{{ $ongoingOrdersCount }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Commandes validées (aujourd'hui)</h5>
                            <p>{{ $validatedOrdersCount }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>Recettes journalières</h5>
                            <p>{{ number_format($dailyRevenue, 2) }} FCFA</p>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="chart-card">
                            <h5>Nombre de commandes par mois</h5>
                            <canvas id="ordersPerMonthChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-card">
                            <h5>Commandes par catégorie (ce mois)</h5>
                            <canvas id="productsPerCategoryChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-card">
                            <h5>Quantité par burger (ce mois)</h5>
                            <canvas id="productsPerBurgerChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Commandes récentes -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Commandes récentes</h4>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">Voir toutes les commandes</a>
                    </div>
                    <div class="card-body">
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
                                @foreach ($recentOrders as $order)
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Graphique : Nombre de commandes par mois
        const ordersPerMonthCtx = document.getElementById('ordersPerMonthChart').getContext('2d');
        new Chart(ordersPerMonthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Commandes',
                    data: @json(array_values($ordersPerMonthData)),
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Ajout pour permettre un contrôle précis de la hauteur
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5
                    }
                }
            }
        });

        // Graphique : Commandes par catégorie
        const productsPerCategoryCtx = document.getElementById('productsPerCategoryChart').getContext('2d');
        new Chart(productsPerCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($productsPerCategory)),
                datasets: [{
                    data: @json(array_values($productsPerCategory)),
                    backgroundColor: ['#f97316', '#ffcc00'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Désactiver pour forcer la hauteur
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Graphique : Quantité par burger
        const productsPerBurgerCtx = document.getElementById('productsPerBurgerChart').getContext('2d');
        new Chart(productsPerBurgerCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($productsPerBurger)),
                datasets: [{
                    label: 'Quantité',
                    data: @json(array_values($productsPerBurger)),
                    backgroundColor: '#f97316',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Ajout pour permettre un contrôle précis de la hauteur
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>
