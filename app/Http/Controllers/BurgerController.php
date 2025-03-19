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
        $burgers = Burger::all();
        return view('burgers.index', compact('burgers'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('burgers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ]);

        $data = $validated;

        // Je gére ici l'upload de l'image
        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('burgers', 'public');
            if($imagePath){
                $data['image'] = $imagePath;
            } else {
                $data['image'] = null;
            }
        }

        Burger::create($data);

        return redirect()->route('burgers.index')->with('success', 'Burger ajouté avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Burger $burger)
    {
        return view('burgers.edit', compact('burger'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Burger $burger)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ]);

        $data = $validated;

        // Pareille aussi ici. Je gére l'upload de l'image.
        if($request->hasFile('image')) {
            // Je supprime l'ancienne image si elle existe
            if($burger->image) {
                storage::disk('public')->delete($burger->image);
            }
            $imagePath = $request->file('image')->store('burgers', 'public');
            $data['image'] = $imagePath;
        }
        $burger->update($data);

        return redirect()->route('burgers.index')->with('success', 'Burger mis à jour avec succès.');
    }

    public function archive(Burger $burger)
    {
        $burger->update(['archive' => true]);
        return redirect()->route('burgers.index')->with('success', 'Burger archivé avec succés.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Burger $burger)
    {
        $burger->delete();
        return redirect()->route('burgers.index')->with('success', 'Burger supprimé avec succés.');
    }
}
