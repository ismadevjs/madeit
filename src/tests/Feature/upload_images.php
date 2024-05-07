<?php

use App\Models\File; // Import the File model
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads a file', function () {
    // Create a file with a valid provider ID and generate an associated UploadedFile instance
    $file = File::factory()->create();

    // Make a POST request to the file upload route
    $response = $this->post("/upload/file", [
        'name' => 'Test File',
        'provider_id' => $file->provider_id, // Use the provider_id from the created file
        'file' => UploadedFile::fake()->create('test_file.jpg'), // Generate an UploadedFile instance
        'type' => 'image', // Specify the type as "image"
    ]);

    // Assert that the response has a status code of 201 (created)
    $response->assertStatus(201);

    // Assert that the response JSON contains the uploaded file details
    $response->assertJson([
        'name' => 'Test File',
        'provider_id' => $file->provider_id, // Use the provider_id from the created file
        'type' => 'image',
    ]);

    // Assert that the file was stored in the expected location
    $this->assertFileExists('files/' . $file->hashName());
});
