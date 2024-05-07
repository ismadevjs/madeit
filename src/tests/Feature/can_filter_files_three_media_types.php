<?php


it('user can filter files by video type', function () {
    $response = $this->get('/api/files?mediaType=video');
    $response->assertStatus(200);
});

it('user can filter files by image type', function () {
    $response = $this->get('/api/files?mediaType=image');
    $response->assertStatus(200);
});

it('user can filter files by audio type', function () {
    $response = $this->get('/api/files?mediaType=audio');
    $response->assertStatus(200);
});

it('user cannot filter files by other types', function () {
    $randomMediaType = generateRandomMediaType();
    $response = $this->get("/api/files?mediaType=$randomMediaType");
    $response->assertStatus(404);
});

function generateRandomMediaType()
{
    $types = ['blah', 'games', 'anything'];
    $randomIndex = array_rand($types);
    return $types[$randomIndex];
}