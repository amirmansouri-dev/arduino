<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Query;

class QuerySeeder extends Seeder
{
    public function run()
    {
        Query::create([
            'question' => 'tomates',
        ]);

        Query::create([
            'question' => 'plantation d\'arbres',
        ]);

        Query::create([
            'question' => 'oliviers',
        ]);

        Query::create([
            'question' => 'programme d\'arrosage',
        ]);

        Query::create([
            'question' => 'taille des oliviers',
        ]);

        Query::create([
            'question' => 'fertilisation des oliviers',
        ]);

        Query::create([
            'question' => 'lumière pour les plantes',
        ]);

        Query::create([
            'question' => 'température pour les semis',
        ]);

        Query::create([
            'question' => 'utilisation des pesticides',
        ]);

        Query::create([
            'question' => 'récolte des légumes',
        ]);

        Query::create([
            'question' => 'protection contre les maladies',
        ]);

        Query::create([
            'question' => 'lutte contre les insectes',
        ]);

        Query::create([
            'question' => 'culture sous serre',
        ]);

        Query::create([
            'question' => 'techniques de jardinage',
        ]);
    }
}
