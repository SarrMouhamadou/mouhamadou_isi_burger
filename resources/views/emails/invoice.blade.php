@component('mail::message')
# Facture - Commande #{{ $order->id }}

Bonjour {{ $order->user->name }},

Merci pour votre commande chez **ISI Burger** ! Voici les détails de votre facture :

## Détails de la commande
- **Numéro de commande** : {{ $order->id }}
- **Date** : {{ $order->created_at->format('d/m/Y') }}
- **Paiement** : Espèces
- **Statut** : {{ $order->status }}

## Informations du client
- **Nom** : {{ $order->user->name }}
- **Email** : {{ $order->user->email }}
- **Adresse** : Diamalaye
- **Téléphone** : 709641912

## Détails des articles

@component('mail::table')
| Description        | Qté | Prix Unitaire | Total     |
|--------------------|-----|---------------|-----------|
@foreach($order->burgers as $burger)
| {{ $burger->name }} | {{ $burger->pivot->quantity }} | {{ number_format($burger->price, 2) }} FCFA | {{ number_format($burger->pivot->quantity * $burger->price, 2) }} FCFA |
@endforeach
| **Sous-total**     |     |               | **{{ number_format($order->burgers->sum(fn($burger) => $burger->pivot->quantity * $burger->price), 2) }} FCFA** |
| **Frais de livraison** |     |               | **1,000.00 FCFA** |
| **Total**          |     |               | **{{ number_format($order->total_amount, 2) }} FCFA** |
@endcomponent

@component('mail::panel')
Merci de nous avoir choisis ! Si vous avez des questions, n'hésitez pas à nous contacter à **msarmoustapha@gmail.com** ou au **709641912**.
@endcomponent

Cordialement,
**ISI Burger**

@component('mail::footer')
© {{ date('Y') }} ISI Burger. Tous droits réservés.
@endcomponent
@endcomponent
