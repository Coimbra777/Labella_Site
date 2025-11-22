<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Vestido Floral Elegante',
                'description' => 'Vestido floral com corte moderno e elegante, perfeito para ocasiões especiais.',
                'short_description' => 'Vestido floral elegante e moderno',
                'price' => 199.90,
                'compare_price' => 249.90,
                'sku' => 'VEST-001',
                'quantity' => 15,
                'is_active' => true,
                'is_featured' => true,
                'images' => ['product-01.jpg', 'product-02.jpg'],
                'sizes' => ['P', 'M', 'G', 'GG'],
                'colors' => ['Rosa', 'Azul', 'Branco'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Blusa Manga Longa Premium',
                'description' => 'Blusa de manga longa em tecido premium, confortável e versátil.',
                'short_description' => 'Blusa manga longa premium',
                'price' => 89.90,
                'compare_price' => 119.90,
                'sku' => 'BLUS-001',
                'quantity' => 25,
                'is_active' => true,
                'is_featured' => true,
                'images' => ['product-03.jpg'],
                'sizes' => ['P', 'M', 'G'],
                'colors' => ['Preto', 'Branco', 'Bege'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Calça Jeans Skinny',
                'description' => 'Calça jeans skinny de alta qualidade, modelagem perfeita.',
                'short_description' => 'Calça jeans skinny',
                'price' => 149.90,
                'sku' => 'CALC-001',
                'quantity' => 20,
                'is_active' => true,
                'is_featured' => false,
                'images' => ['product-04.jpg'],
                'sizes' => ['36', '38', '40', '42', '44'],
                'colors' => ['Azul Claro', 'Azul Escuro'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Saia Midi Plissada',
                'description' => 'Saia midi plissada elegante, ideal para o escritório ou eventos.',
                'short_description' => 'Saia midi plissada',
                'price' => 129.90,
                'compare_price' => 159.90,
                'sku' => 'SAIA-001',
                'quantity' => 18,
                'is_active' => true,
                'is_featured' => true,
                'images' => ['product-05.jpg'],
                'sizes' => ['P', 'M', 'G'],
                'colors' => ['Preto', 'Cinza', 'Verde'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Vestido Casual Curto',
                'description' => 'Vestido casual curto, perfeito para o dia a dia.',
                'short_description' => 'Vestido casual curto',
                'price' => 99.90,
                'sku' => 'VEST-002',
                'quantity' => 30,
                'is_active' => true,
                'is_featured' => false,
                'images' => ['product-06.jpg'],
                'sizes' => ['P', 'M', 'G', 'GG'],
                'colors' => ['Rosa', 'Amarelo', 'Azul'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Blusa Regata Básica',
                'description' => 'Blusa regata básica, essencial no guarda-roupa.',
                'short_description' => 'Blusa regata básica',
                'price' => 49.90,
                'sku' => 'BLUS-002',
                'quantity' => 50,
                'is_active' => true,
                'is_featured' => false,
                'images' => ['product-07.jpg'],
                'sizes' => ['P', 'M', 'G'],
                'colors' => ['Branco', 'Preto', 'Cinza', 'Rosa'],
                'sort_order' => 6,
            ],
        ];

        foreach ($products as $index => $productData) {
            // Assign category based on product name
            $categoryName = match(true) {
                str_contains($productData['name'], 'Vestido') => 'Vestidos',
                str_contains($productData['name'], 'Blusa') => 'Blusas',
                str_contains($productData['name'], 'Calça') => 'Calças',
                str_contains($productData['name'], 'Saia') => 'Saias',
                default => 'Vestidos',
            };

            $category = $categories->firstWhere('name', $categoryName) ?? $categories->first();
            $productData['category_id'] = $category->id;
            $productData['slug'] = Str::slug($productData['name']);

            Product::create($productData);
        }
    }
}
