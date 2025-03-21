<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Historique des commandes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
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
            margin: 0;
        }

        .container-fluid {
            padding: 0;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-bottom: 1px solid #eee;
        }

        .logo {
            color: var(--primary);
            font-weight: 700;
            font-size: 24px;
        }

        .logo span {
            color: var(--text-dark);
        }

        .sidebar {
            background-color: white;
            border-right: 1px solid #eee;
            min-height: 100vh;
        }

        .sidebar-menu .nav-link {
            color: var(--text-light);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .sidebar-menu .nav-link.active {
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .status-en-attente {
            background-color: #ffcc00;
            color: white;
        }

        .status-en-préparation {
            background-color: #007bff;
            color: white;
        }

        .status-prête {
            background-color: #28a745;
            color: white;
        }

        .status-payée {
            background-color: #17a2b8;
            color: white;
        }

        .status-annulée {
            background-color: #dc3545;
            color: white;
        }

        .burger-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .burger-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .burger-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="header-section">
            <div class="logo">
                <i class="bi bi-circle-fill"></i> ISI<span>Burger</span>
            </div>
        </div>

        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-4">
                <div class="sidebar-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <i class="bi bi-grid"></i> Catalogue des burgers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}">
                                <i class="bi bi-cart"></i> Panier
                                @php
                                    $cart = session('cart', []);
                                    $cartCount = array_sum(array_column($cart, 'quantity'));
                                @endphp
                                @if ($cartCount > 0)
                                    <span class="cart-count" id="cart-count">{{ $cartCount }}</span>
                                @else
                                    <span class="cart-count" id="cart-count" style="display: none;">0</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i> Historique des commandes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.index') ? 'active' : '' }}">
                                <i class="bi bi-credit-card"></i> Détails des paiements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2>Historique des commandes</h2>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($orders->isEmpty())
                    <p>Vous n'avez aucune commande pour le moment.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Burgers</th>
                                <th>Quantité totale</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        <ul class="burger-list">
                                            @foreach ($order->burgers as $burger)
                                                <li>
                                                    @if ($burger->image)
                                                        <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" class="burger-image">
                                                    @else
                                                        <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut" class="burger-image">
                                                    @endif
                                                    <span>{{ $burger->name }} (x{{ $burger->pivot->quantity }})</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ $order->burgers->sum('pivot.quantity') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} FCFA</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">Détails</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                @endif

                <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Retour au catalogue</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
