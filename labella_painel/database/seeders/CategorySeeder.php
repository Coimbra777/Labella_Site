<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Vestidos',
                'description' => 'Vestidos elegantes e modernos para todos os momentos',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Blusas',
                'description' => 'Blusas e camisetas com estilo e conforto',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Calças',
                'description' => 'Calças e pantalonas para o dia a dia',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Saias',
                'description' => 'Saias versáteis e elegantes',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Acessórios',
                'description' => 'Acessórios para complementar seu look',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['slug'] = Str::slug($categoryData['name']);
            Category::create($categoryData);
        }
    }
}
