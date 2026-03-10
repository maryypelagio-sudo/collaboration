<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Office Supplies', 'description' => 'Pens, paper, and other stationery.'],
            ['name' => 'Electronics', 'description' => 'Laptops, monitors, and cables.'],
            ['name' => 'Furniture', 'description' => 'Desks, chairs, and cabinets.'],
            ['name' => 'Cleaning Supplies', 'description' => 'Detergents, mops, and brushes.'],
        ];

        foreach ($categories as $cat) {
            $category = Category::create($cat);

            if ($cat['name'] === 'Office Supplies') {
                Item::create([
                    'category_id' => $category->id,
                    'name' => 'A4 Paper',
                    'sku' => 'SUP-PAPER-A4',
                    'quantity' => 50,
                    'unit' => 'reams',
                    'min_stock_level' => 10,
                ]);
            }

            if ($cat['name'] === 'Electronics') {
                Item::create([
                    'category_id' => $category->id,
                    'name' => 'USB-C Cable 2m',
                    'sku' => 'ELE-CABLE-USBC',
                    'quantity' => 20,
                    'unit' => 'pcs',
                    'min_stock_level' => 5,
                ]);
            }
        }
    }
}
