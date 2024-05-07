<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Google', 'Snapchat']),
            'description' => function (array $attributes) {
                return $attributes['name'] === 'Google' ? 
                    'Must be in aspect ratio 4:3, < 2 MB size for .jpg, < 1 minute long for .mp4, < 30 seconds long for .mp3' :
                    'Must be in aspect ratio 16:9, < 5 MB in size for .jpg and .gif, < 50 MB in size for .mp4 and .mov, < 5 minutes long for .mp4 and .mov';
            },
        ];
    }
}
