<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Gestionnaire - Commandes</title>
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
                    <a class="nav-link active" href="{{ route('gestionnaire.orders.index') }}">Commandes</a>
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
                <h2>Liste des Commandes</h2>
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
                <div class="card-header">
                    <h4>Commandes</h4>
                </div>
                <div class="card-body">
                    @if ($orders->isEmpty())
                        <p>Aucune commande disponible.</p>
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
                                @foreach ($orders as $order)
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
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
                .then(response => response.json())
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
                            text: 'Erreur lors de la mise à jour du statut.',
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
                        text: 'Une erreur s\'est produite.',
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
