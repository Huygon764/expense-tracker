<?php

namespace Database\Seeders;

use App\Models\DefaultCategory;
use Illuminate\Database\Seeder;

class DefaultCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $items = config('onboarding.default_categories', []);

        foreach ($items as $item) {
            DefaultCategory::firstOrCreate(
                ['name' => $item['name']],
                [
                    'icon' => $item['icon'] ?? null,
                    'color' => $item['color'] ?? null,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]
            );
        }
    }
}
