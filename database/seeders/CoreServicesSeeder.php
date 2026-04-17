<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class CoreServicesSeeder extends Seeder
{
    public function run(): void
    {
        $core = [
            [
                'title' => 'Full Service',
                'price' => 180,
                'sort_order' => 1,
            ],
            [
                'title' => 'Drop-Off Service',
                'price' => 150,
                'sort_order' => 2,
            ],
            [
                'title' => 'Self-Service',
                'price' => 110,
                'sort_order' => 3,
            ],
        ];

        foreach ($core as $cfg) {
            Service::updateOrCreate(
                ['title' => $cfg['title']], // identify by title
                [
                    'description' => $cfg['title'] . ' (default service)',
                    'price'       => $cfg['price'],
                    'is_active'   => true,
                    'sort_order'  => $cfg['sort_order'],
                ]
            );
        }
    }
}
