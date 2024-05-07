<?php
use Faker\Factory as FakerFactory;


it('returns 302 redirect when posting without body params', function () {
    // Make a POST request without any body parameters and with Content-Type header
    $response = $this->post('/api/upload/image');

    // Assert that the response has a status code of 422
    $response->assertStatus(302);
});

it('should not upload a file with random parameters or wrong params', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = rand(1, 2);
    
    // Make a POST request to the image upload route with random parameters
    $response = $this->post("/api/upload/image", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'image',
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(302);
});

it('should not upload a file with random parameters or wrong params for video', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = rand(1, 2);
    
    // Make a POST request to the video upload route with random parameters
    $response = $this->post("/api/upload/video", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'video',
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(302);
});


it('upload wrong aspect ratio local image to Google and Snapchat provider', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = 1;
    $file = realpath(__DIR__ . '/../files/AlBK6ASsa7dM9bbLRZDoGGMkVhxqnaR4NI42IFNN.jpg');
    
    // Make a POST request to the image upload route with random parameters and local file
    $response = $this->followingRedirects()->post("/api/upload/image", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'image',
        'file' => new \Illuminate\Http\UploadedFile($file, 'test_file.jpg', 'image/jpeg', null, true),
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(400);
});



it('upload local image to Snapchat provider', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = 2;
    $file = realpath(__DIR__ . '/../files/AlBK6ASsa7dM9bbLRZDoGGMkVhxqnaR4NI42IFNN.jpg');
    
    // Make a POST request to the video upload route with random parameters and local file
    $response = $this->followingRedirects()->post("/api/upload/video", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'image',
        'file' => new \Illuminate\Http\UploadedFile($file, 'test_file.jpg', 'image/jpeg', null, true),
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(200);
});


it('upload image with wrong file type', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = rand(1 ,2);
    $file = realpath(__DIR__ . '/../files/AlBK6ASsa7dM9bbLRZDoGGMkVhxqnaR4NI42IFNN.jpg');
    
    // Make a POST request to the video upload route with random parameters and local file
    $response = $this->followingRedirects()->post("/api/upload/video", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'video',
        'file' => new \Illuminate\Http\UploadedFile($file, 'test_file.jpg', 'image/jpeg', null, true),
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(400);
});

it('upload video with wrong file type', function () {
    // Generate random parameters
    $faker = FakerFactory::create();
    $name = $faker->word;
    $providerId = rand(1 ,2);
    $file = realpath(__DIR__ . '/../files/AlBK6ASsa7dM9bbLRZDoGGMkVhxqnaR4NI42IFNN.jpg');
    
    // Make a POST request to the video upload route with random parameters and local file
    $response = $this->followingRedirects()->post("/api/upload/video", [
        'name' => $name,
        'provider_id' => $providerId,
        'type' => 'audio',
        'file' => new \Illuminate\Http\UploadedFile($file, 'test_file.jpg', 'image/jpeg', null, true),
    ]);

    // Assert that the response has a status code of 422 (unprocessable entity)
    $response->assertStatus(400);
});
