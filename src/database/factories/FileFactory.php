<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'provider_id' => 1, // Provider ID is fixed at 1
            'file' => $this->faker->imageUrl(), // Use faker to generate a random image URL
            'type' => 'image', // Type is fixed as 'image'
        ];
    }
}
