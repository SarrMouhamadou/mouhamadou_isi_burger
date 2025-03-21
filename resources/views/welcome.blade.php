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
        }

        .browser-frame {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px auto;
            max-width: 1200px;
        }

        .browser-header {
            background-color: #f0f0f0;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .browser-actions {
            display: flex;
            gap: 6px;
            margin-right: 15px;
        }

        .browser-dot {
            height: 12px;
            width: 12px;
            border-radius: 50%;
        }

        .browser-dot-red {
            background-color: #ff6057;
        }

        .browser-dot-yellow {
            background-color: #ffbd2e;
        }

        .browser-dot-green {
            background-color: #27c93f;
        }

        .browser-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #999;
            margin-right: 10px;
        }

        .browser-address {
            background-color: #e8e8e8;
            border-radius: 15px;
            padding: 5px 15px;
            flex-grow: 1;
            color: #777;
            font-size: 14px;
        }

        .browser-controls {
            display: flex;
            gap: 15px;
            margin-left: 10px;
            color: #999;
        }

        .sidebar {
            background-color: white;
            border-right: 1px solid #eee;
            min-height: calc(100vh - 160px);
        }

        .logo {
            color: var(--primary);
            font-weight: 700;
            font-size: 24px;
        }

        .logo span {
            color: var(--text-dark);
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

        .search-bar {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .filter-button {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
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

        .order-summary {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
        }

        .payment-method {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-option {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .payment-option.active {
            border-color: var(--primary);
        }

        .payment-option img {
            height: 40px;
        }

        /* Tutorial section */
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
    <!-- Browser frame -->
    <div class="browser-frame">
        <div class="browser-header">
            <div class="browser-actions">
                <div class="browser-dot browser-dot-red"></div>
                <div class="browser-dot browser-dot-yellow"></div>
                <div class="browser-dot browser-dot-green"></div>
            </div>
            <div class="browser-nav">
                <i class="bi bi-chevron-left"></i>
                <i class="bi bi-chevron-right"></i>
            </div>
            <div class="browser-address">
                <i class="bi bi-lock"></i> isiburger.com
            </div>
            <div class="browser-controls">
                <i class="bi bi-arrow-clockwise"></i>
                <i class="bi bi-share"></i>
                <i class="bi bi-plus"></i>
                <i class="bi bi-grid-3x3-gap"></i>
            </div>
        </div>

        <div class="container-fluid p-0">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-md-2 sidebar p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="logo me-2">
                            <i class="bi bi-circle-fill"></i> ISI<span>Burger</span>
                        </div>
                    </div>

                    <div class="sidebar-menu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="nav-link active">
                                    <i class="bi bi-grid"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('orders.index') }}" class="nav-link">
                                    <i class="bi bi-cart"></i> Mes Commandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-chat-dots"></i> Feedback
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-envelope"></i> Messages
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-clock-history"></i> Historique Commandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-credit-card"></i> Paiements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-sliders"></i> Personnalisation
                                </a>
                            </li>
                            @auth
                                <li class="nav-item">
                                    <a href="{{ route('logout') }}" class="nav-link"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">
                                        <i class="bi bi-box-arrow-in-right"></i> Connexion
                                    </a>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-7 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="search-bar flex-grow-1 me-3">
                            <i class="bi bi-search me-2"></i>
                            <input type="text" placeholder="Rechercher un burger">
                        </div>
                        <button class="filter-button">
                            <i class="bi bi-funnel me-2"></i> Filtrer
                        </button>
                    </div>

                    <!-- Categories -->
                    <div class="mb-4">
                        <h5 class="mb-3">Explorer les Catégories</h5>
                        <div class="row row-cols-5 g-3">
                            @foreach ($categories as $category)
                                <div class="col">
                                    <div class="category-item {{ $category == 'Classique' ? 'active' : '' }}">
                                        <div class="category-icon">🍔</div>
                                        <div class="category-name">{{ $category }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav tabs-nav mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Populaire</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Récent</a>
                        </li>
                    </ul>

                    <!-- Food Cards -->
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach ($burgers as $burger)
                            <div class="col">
                                <div class="food-card">
                                    <div class="food-image">
                                        <div class="food-favorite">
                                            <i class="bi bi-heart{{ in_array($burger->id, $wishlist) ? '-fill' : '' }}"></i>
                                        </div>
                                        <img src="{{ $burger->image ? asset('storage/' . $burger->image) : asset('images/default-burger.png') }}"
                                             alt="{{ $burger->name }}">
                                    </div>
                                    <div class="food-content">
                                        <h5 class="food-title">{{ $burger->name }}</h5>
                                        <div class="food-price">
                                            {{ number_format($burger->price, 2) }} FCFA
                                            @if ($burger->old_price)
                                                <span class="old-price">{{ number_format($burger->old_price, 2) }} FCFA</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="food-rating">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <span>4.8+</span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('wishlist.toggle', $burger->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-light">Wishlist</button>
                                                </form>
                                                <form action="{{ route('cart.add', $burger->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary">Ajouter au Panier</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tutorial Section -->
                    <div class="tutorial-card mt-5">
                        <div class="tutorial-image" style="background-image: url('{{ asset('images/tutorial-order.jpg') }}');"></div>
                        <div class="tutorial-content">
                            <h5>Comment commander un burger ?</h5>
                            <p class="tutorial-steps">
                                Commander un burger sur ISI Burger est simple et rapide. Quelques clics suffisent et votre burger sera livré en 35-45 minutes.
                            </p>
                            <div class="tutorial-dots">
                                <div class="tutorial-dot active"></div>
                                <div class="tutorial-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-md-3 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            @auth
                                <img src="{{ asset('images/user-avatar.png') }}" alt="User" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                <div>{{ Auth::user()->name }}</div>
                            @else
                                <div>Invité</div>
                            @endauth
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </div>

                    <div class="order-summary mb-4">
                        <h5 class="mb-3">Panier</h5>

                        <div class="order-items">
                            @if (session('cart', [])->isEmpty())
                                <p>Votre panier est vide.</p>
                            @else
                                @foreach (session('cart', []) as $id => $details)
                                    <div class="order-item">
                                        <img src="{{ $details['image'] ? asset('storage/' . $details['image']) : asset('images/default-burger.png') }}"
                                             alt="{{ $details['name'] }}">
                                        <div>
                                            <div class="fw-bold">{{ $details['name'] }}</div>
                                            <div class="text-danger">{{ number_format($details['price'] * $details['quantity'], 2) }} FCFA</div>
                                            <div>Quantité: {{ $details['quantity'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <div>Sous-total</div>
                            <div>{{ number_format($subtotal, 2) }} FCFA</div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div>Taxe</div>
                            <div>+{{ number_format($tax, 2) }} FCFA</div>
                        </div>

                        <div class="d-flex justify-content-between fw-bold mb-4">
                            <div>Total</div>
                            <div>{{ number_format($total, 2) }} FCFA</div>
                        </div>

                        <div>
                            <div class="mb-3">Méthode de Paiement</div>
                            <div class="payment-method">
                                <div class="payment-option active">
                                    <img src="{{ asset('images/paypal.png') }}" alt="PayPal">
                                </div>
                                <div class="payment-option">
                                    <img src="{{ asset('images/mobile-money.png') }}" alt="Mobile Money">
                                </div>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="payment-option">
                                        <img src="{{ asset('images/mastercard.png') }}" alt="Mastercard">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="payment-option">
                                        <img src="{{ asset('images/visa.png') }}" alt="Visa">
                                    </div>
                                </div>
                            </div>

                            @if (session('cart', [])->isNotEmpty())
                                <form action="{{ route('order.place') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 py-3">Passer la Commande</button>
                                </form>
                            @else
                                <button class="btn btn-primary w-100 py-3" disabled>Passer la Commande</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
