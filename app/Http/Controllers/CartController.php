<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05; // Taxe de 5%
        $total = $subtotal + $tax;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request, $id)
    {
        $burger = Burger::findOrFail($id);

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $burger->name,
                'price' => $burger->price,
                'quantity' => 1,
                'image' => $burger->image,
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Burger ajouté au panier !');
    }

    public function update(Request $request, $id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $quantity = $request->input('quantity', 1);
            if ($quantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
            }
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Quantité mise à jour !');
        }

        return redirect()->route('cart.index')->with('error', 'Article non trouvé dans le panier.');
    }

    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Article supprimé du panier !');
        }

        return redirect()->route('cart.index')->with('error', 'Article non trouvé dans le panier.');
    }

    public function toggleWishlist(Request $request, $id)
    {
        $wishlist = Session::get('wishlist', []);

        if (in_array($id, $wishlist)) {
            $wishlist = array_diff($wishlist, [$id]);
        } else {
            $wishlist[] = $id;
        }

        Session::put('wishlist', $wishlist);

        return redirect()->route('home')->with('success', 'Wishlist mise à jour !');
    }
}
