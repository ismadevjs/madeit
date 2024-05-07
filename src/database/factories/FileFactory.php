<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get all provider IDs
        $providerIds = Provider::pluck('id')->toArray();

        // If there are no providers available, create one and get its ID
        if (empty($providerIds)) {
            $provider = Provider::factory()->create();
            $providerIds[] = $provider->id;
        }

      

        // Define file types
        $types = ['image', 'video', 'audio'];

        // Choose a random provider ID and type for the file
        $providerId = $this->faker->randomElement($providerIds);
        $type = $this->faker->randomElement($types);

        // Define file extensions based on types and provider
        $extensions = [
            'image' => 'jpg',
            'video' => $providerId === 2 ? ['mp4', 'mov'] : 'mp4',
            'audio' => 'mp3',
        ];

        // Generate a fake file name with the corresponding extension
        $fileName = $this->faker->name . '.' . (is_array($extensions[$type]) ?
            $this->faker->randomElement($extensions[$type]) :
            $extensions[$type]);

        return [
            'name' => $fileName,
            'provider_id' => $providerId, // Assign the provider_id here
            'file' => $this->generateFile($type, $providerId),
            'thumbnail' => $type === 'video' ? $this->generateThumbnail() : null,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }


    /**
     * Generate a fake file path based on the type and provider.
     *
     * @param  string  $type
     * @param  int  $providerId
     * @return string
     */
    private function generateFile(string $type, int $providerId): string
    {
        // Generate a fake file path based on type and provider
        if ($type === 'image') {
            return $this->faker->imageUrl();
        } elseif ($type === 'video') {
            // Randomly select a video URL from the provided JSON object
            $videoUrls = [
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4",
                "http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4"
            ];

            return $this->faker->randomElement($videoUrls);
        } elseif ($type === 'audio') {
            return $this->faker->url . '.mp3';
        }
    }

    /**
     * Generate a fake thumbnail URL.
     *
     * @return string
     */
    private function generateThumbnail(): string
    {
        return $this->faker->imageUrl();
    }
}
