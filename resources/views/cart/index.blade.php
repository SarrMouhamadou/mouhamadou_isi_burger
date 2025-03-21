<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Panier</title>
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
                <h2>Panier</h2>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (count($cart) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Burger</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $id => $item)
                                <tr>
                                    <td>
                                        @if ($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                        @else
                                            <img src="{{ asset('images/default-burger.png') }}" alt="Burger par défaut" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                        @endif
                                    </td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ number_format($item['price'], 2) }} FCFA</td>
                                    <td>
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" style="width: 60px;" class="form-control d-inline">
                                            <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
                                        </form>
                                    </td>
                                    <td>{{ number_format($item['price'] * $item['quantity'], 2) }} FCFA</td>
                                    <td>
                                        <a href="{{ route('cart.remove', $id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-end">
                        <p><strong>Sous-total :</strong> {{ number_format($subtotal, 2) }} FCFA</p>
                        <p><strong>Taxe (5%) :</strong> {{ number_format($tax, 2) }} FCFA</p>
                        <p><strong>Total :</strong> {{ number_format($total, 2) }} FCFA</p>
                        <form action="{{ route('order.place') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Passer la commande</button>
                        </form>
                    </div>
                @else
                    <p>Votre panier est vide.</p>
                @endif

                <a href="{{ route('home') }}" class="btn btn-secondary mt-3">Retour au catalogue</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
