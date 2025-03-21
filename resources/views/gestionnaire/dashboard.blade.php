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
        .chart-card canvas { max-height: 200px !important; width: 100% !important; height: 200px !important; }
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
                    <a class="nav-link" href="{{ route('gestionnaire.orders.index') }}">Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payments.index') }}">Paiements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="statistiques">Statistiques</a>
                </li>
                <!-- Ajout de la section Burger -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('burgers.index') }}">Burgers</a>
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
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h5>Nombre de clients</h5>
                            <p>{{ $clientCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h5>Commandes en cours (aujourd'hui)</h5>
                            <p>{{ $ongoingOrdersCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h5>Commandes validées (aujourd'hui)</h5>
                            <p>{{ $validatedOrdersCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h5>Recettes journalières</h5>
                            <p>{{ number_format($dailyRevenue ?? 0, 2) }} FCFA</p>
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
                            @if (array_sum($productsPerCategory) > 0)
                                <canvas id="productsPerCategoryChart"></canvas>
                            @else
                                <p>Aucune donnée disponible pour ce mois.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="chart-card">
                            <h5>Quantité par burger (ce mois)</h5>
                            @if (!empty($productsPerBurger))
                                <canvas id="productsPerBurgerChart"></canvas>
                            @else
                                <p>Aucune donnée disponible pour ce mois.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Graphique des burgers populaires -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="chart-card">
                            <h5>Burgers les plus populaires</h5>
                            @if (!empty($popularBurgers))
                                <canvas id="popularBurgersChart"></canvas>
                            @else
                                <p>Aucune donnée disponible.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Commandes récentes -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Commandes récentes</h4>
                        <a href="{{ route('gestionnaire.orders.index') }}" class="btn btn-primary btn-sm">Voir toutes les commandes</a>
                    </div>
                    <div class="card-body">
                        @if ($recentOrders->isEmpty())
                            <p>Aucune commande récente.</p>
                        @else
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
                                        <tr data-order-id="{{ $order->id }}">
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
                                            <td>{{ number_format($order->total_amount ?? 0, 2) }} FCFA</td>
                                            <td>{{ $order->user ? $order->user->name : 'N/A' }}</td>
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
                                                                <button class="dropdown-item update-status" data-status="En attente">En attente</button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item update-status" data-status="En préparation">En préparation</button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item update-status" data-status="Prête">Prête</button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item update-status" data-status="Payée">Payée</button>
                                                            </li>
                                                            <li>
                                                                <button class="dropdown-item text-danger update-status" data-status="Annulée" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">Annuler</button>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <!-- Burgers les plus populaires -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Burgers les plus populaires</h4>
                    </div>
                    <div class="card-body">
                        @if ($popularBurgers->isEmpty())
                            <p>Aucun burger populaire pour le moment.</p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Nom</th>
                                        <th>Quantité totale commandée</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($popularBurgers as $burger)
                                        <tr>
                                            <td>
                                                @if ($burger->image)
                                                    <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                @endif
                                            </td>
                                            <td>{{ $burger->name }}</td>
                                            <td>{{ $burger->total_quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
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
                    data: @json(array_values($ordersPerMonthData ?? array_fill(1, 12, 0))),
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5
                    }
                }
            }
        });

        // Graphique : Commandes par catégorie
        const productsPerCategoryCtx = document.getElementById('productsPerCategoryChart')?.getContext('2d');
        if (productsPerCategoryCtx) {
            new Chart(productsPerCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($productsPerCategory ?? ['Burgers' => 0, 'Autres' => 0])),
                    datasets: [{
                        data: @json(array_values($productsPerCategory ?? ['Burgers' => 0, 'Autres' => 0])),
                        backgroundColor: ['#f97316', '#ffcc00'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Graphique : Quantité par burger
        const productsPerBurgerCtx = document.getElementById('productsPerBurgerChart')?.getContext('2d');
        if (productsPerBurgerCtx) {
            new Chart(productsPerBurgerCtx, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($productsPerBurger ?? [])),
                    datasets: [{
                        label: 'Quantité',
                        data: @json(array_values($productsPerBurger ?? [])),
                        backgroundColor: '#f97316',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
        }

        // Graphique : Burgers les plus populaires
        const popularBurgersCtx = document.getElementById('popularBurgersChart')?.getContext('2d');
        if (popularBurgersCtx) {
            new Chart(popularBurgersCtx, {
                type: 'bar',
                data: {
                    labels: @json($popularBurgers->pluck('name')),
                    datasets: [{
                        label: 'Quantité commandée',
                        data: @json($popularBurgers->pluck('total_quantity')),
                        backgroundColor: '#f97316',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
        }

        // Gestion des mises à jour de statut via AJAX
        document.querySelectorAll('.update-status').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.closest('tr').dataset.orderId;
                const status = this.dataset.status;
                const statusBadge = this.closest('tr').querySelector('.status-badge');

                fetch(`/gestionnaire/orders/${orderId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: status }),
                })
                .then(response => {
                    // Vérifier le statut de la réponse
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Accès non autorisé. Vérifiez que vous avez le rôle de gestionnaire.');
                        }
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(data.errors.status ? data.errors.status[0] : 'Erreur de validation');
                            });
                        }
                        throw new Error(`Erreur HTTP : ${response.status} - ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le badge de statut
                        statusBadge.textContent = status;
                        statusBadge.className = `status-badge status-${status.toLowerCase().replace(' ', '-')}`;

                        // Afficher une notification
                        Toastify({
                            text: 'Statut mis à jour avec succès !',
                            duration: 3000,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#28a745',
                        }).showToast();

                        // Désactiver les actions si le statut est "Payée" ou "Annulée"
                        if (status === 'Payée' || status === 'Annulée') {
                            const actionsDropdown = this.closest('td').querySelector('.dropdown');
                            actionsDropdown.innerHTML = '<button class="btn btn-sm btn-outline-secondary" disabled>Actions</button>';
                        }
                    } else {
                        Toastify({
                            text: `Erreur lors de la mise à jour du statut : ${data.error || 'Erreur inconnue'}`,
                            duration: 3000,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#dc3545',
                        }).showToast();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Toastify({
                        text: `Une erreur s\'est produite : ${error.message}`,
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#dc3545',
                    }).showToast();
                });
            });
        });
    </script>
</body>
</html>
