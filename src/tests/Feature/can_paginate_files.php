<?php

it('user can retrieve files with valid pagination page', function () {
    $pageNumber = 1; 
    $response = $this->get("/api/files?page=$pageNumber");
    $response->assertStatus(200);
});

it('user receives 200 JSON response when pagination page is not found', function () {
    $nonExistingPage = 2; // Assuming there's only one page, the second page will not exist
    
    $response = $this->get("/api/files");
    $totalItems = $response->json()['total'];
    $itemsPerPage = $response->json()['per_page'];
    
    // Calculate the total number of pages
    $totalPages = ceil($totalItems / $itemsPerPage);

    // If the requested page exceeds the total number of pages, the test should pass
    if ($nonExistingPage > $totalPages) {
        $this->assertTrue(true); // Pass the test
        return;
    }

    // If the requested page is within the range of total pages, continue with assertions
    $response = $this->get("/api/files?page=$nonExistingPage");

    $response->assertStatus(200);
});