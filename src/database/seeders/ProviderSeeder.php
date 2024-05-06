<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Provider::create([
            'name' => 'Google',
            'description' => 'Must be in aspect ratio 4:3, < 2 MB size for .jpg, < 1 minute long for .mp4, < 30 seconds long for .mp3',
        ]);

        Provider::create([
            'name' => 'Snapchat',
            'description' => 'Must be in aspect ratio 16:9, < 5 MB in size for .jpg and .gif, < 50 MB in size for .mp4 and .mov, < 5 minutes long for .mp4 and .mov',
        ]);
    }
}
