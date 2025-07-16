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
            'category_id' => $electronique->id,
        ]);

        Product::create([
            'name' => 'T-shirt Coton Bio',
            'description' => 'Un t-shirt confortable et écologique.',
            'price' => 25.50,
            'stock' => 120,
            'category_id' => $vetements->id,
        ]);

        Product::create([
            'name' => 'Casque Audio Bluetooth',
            'description' => 'Casque avec réduction de bruit active.',
            'price' => 149.99,
            'stock' => 75,
            'category_id' => $electronique->id,
        ]);
    }
}
