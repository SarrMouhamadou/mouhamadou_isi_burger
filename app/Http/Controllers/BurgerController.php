<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;

class BurgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $burgers = Burger::where('archived', false)->latest()->paginate(10);
        return view('gestionnaire.burgers.index', compact('burgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gestionnaire.burgers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('burgers', 'public');
        } else {
            $validated['image'] = null;
        }

        Burger::create($validated);

        return redirect()->route('burgers.index')->with('success', 'Burger ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Burger $burger)
    {
        return view('burgers.show', compact('burger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Burger $burger)
    {
        return view('gestionnaire.burgers.edit', compact('burger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Burger $burger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('burgers', 'public');
        } else {
            $validated['image'] = $burger->image; // Conserver l'image existante si aucune nouvelle image n'est téléchargée
        }

        $burger->update($validated);

        return redirect()->route('burgers.index')->with('success', 'Burger mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Burger $burger)
    {
        $burger->delete();
        return redirect()->route('burgers.index')->with('success', 'Burger supprimé avec succès');
    }
}
