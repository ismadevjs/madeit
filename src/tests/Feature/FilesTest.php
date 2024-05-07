<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;

class FilesTest extends TestCase
{
    public function test_fetching_files_with_pagination_and_filtering()
    {
        // Create test data
        File::factory()->count(15)->create();

        // Set filter and pagination parameters
        $perPage = 5;
        $page = 2;
        $mediaType = 'image';

        // Hit the files endpoint with filter and pagination parameters
        $response = $this->getJson('/files', [
            'perPage' => $perPage,
            'page' => $page,
            'mediaType' => $mediaType,
        ]);

        // Assert response status is OK
        $response->assertOk();

        // Assert correct pagination meta data
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'name', 'provider_name', 'created_at', 'type'], // Added 'type' in assertJsonStructure
            ],
            'from',
            'to',
            'total',
            'per_page',
        ]);

        // Assert correct number of items per page
        $response->assertJsonCount($perPage, 'data');

        // Assert files have the correct media type
        $response->assertJson(function (array $json) use ($mediaType) {
            foreach ($json['data'] as $file) {
                if ($file['type'] !== $mediaType) {
                    return false;
                }
            }
            return true;
        });
    }

}
