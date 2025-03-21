<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Mes Paiements</title>
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
        .invoice-header { background-color: #f97316; color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .invoice-body { background-color: #fff5e6; padding: 20px; border-radius: 0 0 10px 10px; }
        .table thead { background-color: #f97316; color: white; }
        /* Réduire la taille de la facture de 1/3 (longueur et largeur) */
        .invoice-container {
            transform: scale(0.6667); /* Réduction de 1/3 (1 - 0.3333 = 0.6667) */
            transform-origin: top left; /* Point de référence pour la réduction */
            width: 150%; /* Ajuster la largeur pour compenser la réduction */
        }
    </style>
</head>
<body class="h-full">
    <div class="d-flex">
        <!-- Sidebar pour Client -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-center">ISI Burger - Client</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">Panier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('orders.index') }}">Mes Commandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('payments.index') }}">Mes Paiements</a>
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
                <h2>Mes Paiements</h2>
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

            <!-- Liste des paiements sous forme de factures -->
            <div class="card">
                <div class="card-header">
                    <h4>Liste de Mes Paiements</h4>
                </div>
                <div class="card-body">
                    @if ($payments->isEmpty())
                        <p>Aucun paiement disponible pour le moment.</p>
                    @else
                        @foreach ($payments as $payment)
                            <div class="card mb-4 invoice-container">
                                <div class="invoice-header">
                                    <h4 class="mb-0">Facture - Paiement #{{ $payment->id }}</h4>
                                </div>
                                <div class="invoice-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h5>Facturé à :</h5>
                                            <p>
                                                <strong>{{ $payment->order->user->name }}</strong><br>
                                                Adresse : HLM Grand Médine<br>
                                                Tél : 709641912<br>
                                                Email : {{ $payment->order->user->email }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h5>Détails du paiement</h5>
                                            <p>
                                                N° commande : {{ $payment->order->id }}<br>
                                                Date : {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : 'N/A' }}<br>
                                                Paiement : Espèces<br>
                                                Statut : {{ $payment->status }}
                                            </p>
                                        </div>
                                    </div>

                                    <h5>Détails des articles</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Qté</th>
                                                <th>Prix Unitaire</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payment->order->burgers as $burger)
                                                <tr>
                                                    <td>{{ $burger->name }}</td>
                                                    <td>{{ $burger->pivot->quantity }}</td>
                                                    <td>{{ number_format($burger->pivot->unit_price, 2) }} FCFA</td>
                                                    <td>{{ number_format($burger->pivot->quantity * $burger->pivot->unit_price, 2) }} FCFA</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Sous-total</strong></td>
                                                <td><strong>{{ number_format($payment->order->burgers->sum(fn($burger) => $burger->pivot->quantity * $burger->pivot->unit_price), 2) }} FCFA</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                <td><strong>{{ number_format($payment->amount, 2) }} FCFA</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="text-center mt-4">
                                        <p>Merci de nous avoir choisis !</p>
                                        <p>Contactez-nous à <strong>msarmoustapha@gmail.com</strong> ou au <strong>709641912</strong> pour toute question.</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
