<?php

namespace App\Console\Commands;

use App\Models\Burger;
use Illuminate\Console\Command;

class UpdateBurgerCategories extends Command
{
    protected $signature = 'burgers:update-categories';
    protected $description = 'Update categories for existing burgers';

    public function handle()
    {
        // Exemple : attribuer des catégories aux burgers existants
        $burgers = Burger::all();

        foreach ($burgers as $burger) {
            // Exemple de logique pour attribuer une catégorie
            if (str_contains(strtolower($burger->name), 'veggie') || str_contains(strtolower($burger->name), 'végétarien')) {
                $burger->update(['category' => 'Végétarien']);
            } else {
                $burger->update(['category' => 'Classique']);
            }
        }

        $this->info('Categories updated successfully!');
    }
}
