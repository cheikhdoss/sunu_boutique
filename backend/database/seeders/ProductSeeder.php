<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronique = Category::where('slug', 'electronique')->first();
        $vetements = Category::where('slug', 'vetements')->first();

        Product::create([
            'name' => 'Smartphone XYZ',
            'description' => 'Un smartphone dernier cri avec un appareil photo de 108MP.',
            'price' => 799.99,
            'stock' => 50,
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'T-shirt Coton Bio',
            'description' => 'Un t-shirt confortable et écologique.',
            'price' => 25.50,
            'stock' => 120,
            'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&h=300&fit=crop',
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Casque Audio Bluetooth',
            'description' => 'Casque avec réduction de bruit active.',
            'price' => 149.99,
            'stock' => 75,
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'Ordinateur Portable Ultra-fin',
            'description' => 'Un ordinateur portable léger et puissant pour les professionnels.',
            'price' => 1299.99,
            'stock' => 30,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'Jean Slim Fit',
            'description' => 'Un jean moderne et confortable pour toutes les occasions.',
            'price' => 89.90,
            'stock' => 200,
            'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400&h=300&fit=crop',
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Montre Connectée Sport',
            'description' => 'Suivez vos activités sportives avec style.',
            'price' => 199.50,
            'stock' => 60,
            'image' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'Robe d\'été Fleurie',
            'description' => 'Légère et élégante, parfaite pour les journées ensoleillées.',
            'price' => 59.99,
            'stock' => 90,
            'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=300&fit=crop',
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Caméra de Sécurité Intelligente',
            'description' => 'Surveillez votre domicile à distance en HD.',
            'price' => 89.99,
            'stock' => 40,
            'image' => 'https://images.unsplash.com/photo-1588327338044-6d22b95a4389?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'Veste en Cuir',
            'description' => 'Un classique intemporel pour un look rock et chic.',
            'price' => 249.00,
            'stock' => 25,
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=300&fit=crop',
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Enceinte Portable Étanche',
            'description' => 'Emportez votre musique partout, même à la plage.',
            'price' => 69.95,
            'stock' => 110,
            'image' => 'https://images.unsplash.com/photo-1563299796-17596f60a76a?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'Baskets de Course',
            'description' => 'Confort et performance pour vos sessions de running.',
            'price' => 119.99,
            'stock' => 150,
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=300&fit=crop',
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Tablette Graphique pour Dessinateurs',
            'description' => 'Libérez votre créativité avec cette tablette précise et intuitive.',
            'price' => 320.00,
            'stock' => 35,
            'image' => 'https://images.unsplash.com/photo-1558346547-44375f415332?w=400&h=300&fit=crop',
            'category_id' => $electronique->id,
        ]);
    }
}
