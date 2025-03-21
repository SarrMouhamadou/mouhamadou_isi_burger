<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Commande de Burgers en Ligne</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css"
        rel="stylesheet">
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

        .auth-buttons .btn {
            font-size: 14px;
            padding: 5px 15px;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
            justify-content: center;
        }

        .filter-form .form-control {
            font-size: 0.8rem;
            padding: 4px 8px;
            height: 30px;
        }

        .filter-form .btn {
            font-size: 0.8rem;
            padding: 4px 8px;
        }

        .sidebar-left {
            background-color: white;
            border-right: 1px solid #eee;
            min-height: 100vh;
        }

        .sidebar-left .nav-link {
            color: var(--text-light);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .sidebar-left .nav-link:hover,
        .sidebar-left .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .sidebar-left .nav-link.active {
            font-weight: 600;
        }

        .cart-count {
            position: absolute;
            top: 8px;
            right: 10px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 10px;
            background-color: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .category-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .category-item.active {
            background-color: var(--primary);
            color: white;
        }

        .category-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .tabs-nav .nav-item .nav-link {
            color: var(--text-light);
            font-weight: 500;
            padding: 10px 20px;
            border: none;
        }

        .tabs-nav .nav-item .nav-link.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            background-color: transparent;
        }

        .food-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            min-height: 300px;
            display: flex;
            flex-direction: column;
        }

        .food-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .food-image {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9f9f9;
            position: relative;
        }

        .food-image img {
            max-height: 140px;
            max-width: 90%;
        }

        .food-favorite {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: white;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .food-content {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .food-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .food-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .food-price .old-price {
            color: var(--text-light);
            font-weight: 400;
            font-size: 14px;
            text-decoration: line-through;
            margin-left: 5px;
        }

        .food-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text-light);
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-light {
            background-color: #f0f0f0;
            border-color: #f0f0f0;
            color: var(--text-dark);
        }

        .tutorial-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            margin-top: 30px;
        }

        .tutorial-image {
            width: 40%;
            background-size: cover;
            background-position: center;
        }

        .tutorial-content {
            padding: 20px;
            width: 60%;
        }

        .tutorial-steps {
            margin-top: 15px;
            color: var(--text-light);
            font-size: 14px;
        }

        .tutorial-dots {
            display: flex;
            gap: 5px;
            margin-top: 15px;
        }

        .tutorial-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ddd;
        }

        .tutorial-dot.active {
            background-color: var(--primary);
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

            <!-- Filtres dans la barre de navigation -->
            <div class="filter-form">
                <form method="GET" action="{{ route('home') }}" class="d-flex align-items-center gap-2">
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ request('name') }}" placeholder="Nom du burger">
                    <input type="number" class="form-control" id="price_min" name="price_min"
                        value="{{ request('price_min') }}" placeholder="Prix min">
                    <input type="number" class="form-control" id="price_max" name="price_max"
                        value="{{ request('price_max') }}" placeholder="Prix max">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </form>
            </div>

            @if (!Auth::check())
                <div class="auth-buttons">
                    <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Login</button>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                        data-bs-target="#registerModal">Register</button>
                </div>
            @endif
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Connexion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Adresse e-mail</label>
                                <input type="email" class="form-control" id="loginEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Register Modal -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Inscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="registerName" class="form-label">Nom</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="registerName" name="name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="registerUsername" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="registerUsername" name="username" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Adresse e-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="registerEmail" name="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="registerPassword" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="registerPassword" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="registerPasswordConfirmation" class="form-label">Confirmer le mot de
                                    passe</label>
                                <input type="password" class="form-control" id="registerPasswordConfirmation"
                                    name="password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-0">
            <!-- Sidebar Left (affichée uniquement si le client est connecté) -->
            @if (Auth::check())
                <div class="col-md-2 sidebar-left p-4">
                    <div class="sidebar-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="{{ route('home') }}"
                                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    <i class="bi bi-grid"></i> Catalogue des burgers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('cart.index') }}"
                                    class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}">
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
                                <a href="{{ route('orders.index') }}"
                                    class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                                    <i class="bi bi-clock-history"></i> Historique des commandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('payments.index') }}"
                                    class="nav-link {{ request()->routeIs('payments.index') ? 'active' : '' }}">
                                    <i class="bi bi-credit-card"></i> Détails des paiements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('logout') }}" class="nav-link"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="{{ Auth::check() ? 'col-md-10' : 'col-md-12' }} p-4">
                <!-- Categories -->
                <div class="mb-4">
                    <h5 class="mb-3">Explore Category</h5>
                    <div class="row row-cols-8 g-3">
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🍩</div>
                                <div class="category-name">Donuts</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🥟</div>
                                <div class="category-name">Fuchka</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item active">
                                <div class="category-icon">🍔</div>
                                <div class="category-name">Burger</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🍕</div>
                                <div class="category-name">Pizza</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🌭</div>
                                <div class="category-name">Hot dog</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🍦</div>
                                <div class="category-name">Ice</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🍟</div>
                                <div class="category-name">Potato</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="category-item">
                                <div class="category-icon">🍗</div>
                                <div class="category-name">Chicken</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav tabs-nav mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Popular</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Recent</a>
                    </li>
                </ul>

                <!-- Food Cards -->
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @forelse ($burgers as $burger)
                        <div class="col">
                            <div class="food-card">
                                <div class="food-image">
                                    <div class="food-favorite">
                                        <i
                                            class="bi bi-heart{{ in_array($burger->id, $wishlist) ? '-fill' : '' }}"></i>
                                    </div>
                                    <img src="{{ $burger->image ? asset('storage/' . $burger->image) : asset('images/default-burger.png') }}"
                                        alt="{{ $burger->name }}">
                                </div>
                                <div class="food-content">
                                    <h5 class="food-title">{{ $burger->name }}</h5>
                                    <div class="food-price">
                                        {{ number_format($burger->price, 2) }} FCFA
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('burgers.show', $burger->id) }}"
                                                class="btn btn-light">Détails</a>
                                            <form class="add-to-cart-form" data-burger-id="{{ $burger->id }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">Commander</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <p>Aucun burger disponible pour le moment.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Tutorial Section -->
                <div class="tutorial-card mt-5">
                    <div class="tutorial-image"
                        style="background-image: url('{{ asset('images/tutorial-order.jpg') }}');"></div>
                    <div class="tutorial-content">
                        <h5>How to order food?</h5>
                        <p class="tutorial-steps">
                            Ordering food from our web app is a seamless and delightful experience...
                        </p>
                        <div class="tutorial-dots">
                            <div class="tutorial-dot active"></div>
                            <div class="tutorial-dot"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (nécessaire pour AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.add-to-cart-form').on('submit', function (e) {
                e.preventDefault(); // Empêche la soumission classique du formulaire

                var form = $(this);
                var burgerId = form.data('burger-id');

                $.ajax({
                    url: '{{ route("cart.add", ":id") }}'.replace(':id', burgerId),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        // Mettre à jour le compteur du panier
                        var cartCountElement = $('#cart-count');
                        var currentCount = parseInt(cartCountElement.text()) || 0;
                        cartCountElement.text(currentCount + 1);
                        cartCountElement.show(); // Afficher le compteur s'il était caché
                    },
                    error: function (xhr) {
                        alert('Une erreur s\'est produite lors de l\'ajout au panier.');
                    }
                });
            });
        });
    </script>
</body>

</html>
