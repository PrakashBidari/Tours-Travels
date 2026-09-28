<?php

namespace Database\Seeders;

use App\Models\FooterColumn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (FooterColumn::exists()) {
            return;
        }

        $columns = [
            'Company' => [
                ['About us', '/about'],
                ['Careers', '#'],
                ['Press', '#'],
            ],
            'Support' => [
                ['Help Center', '#'],
                ['Cancellation options', '#'],
                ['Contact us', '/contact'],
            ],
            'Legal' => [
                ['Privacy policy', '/privacy-policy'],
                ['Terms of service', '/terms-of-service'],
            ],
        ];

        $position = 0;

        foreach ($columns as $heading => $links) {
            $column = FooterColumn::create([
                'heading' => $heading,
                'position' => $position++,
            ]);

            foreach ($links as $linkPosition => [$label, $url]) {
                $column->links()->create([
                    'label' => $label,
                    'url' => $url,
                    'position' => $linkPosition,
                ]);
            }
        }
    }
}
