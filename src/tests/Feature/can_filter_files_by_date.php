<?php



it('user can filter files by valid upload date', function () {
    $validDate = Carbon\Carbon::today()->toDateString();
    $response = $this->get("/api/files?uploadDate=$validDate");
    $response->assertStatus(200);
});

it('user cannot filter files by invalid upload date', function () {
    $invalidDate = '2023-02-31'; // Invalid date
    $response = $this->get("/api/files?uploadDate=$invalidDate");
    $response->assertStatus(404);
});

it('user cannot filter files by non-existing upload date', function () {
    $nonExistingDate = '2023-04-31'; // Non-existing date
    $response = $this->get("/api/files?uploadDate=$nonExistingDate");
    $response->assertStatus(404);
});

function generateValidDate()
{
    // Generate a random date between today and 30 days from now
    $startDate = strtotime(date('Y-m-d'));
    $endDate = strtotime('+30 days', $startDate);
    $randomTimestamp = mt_rand($startDate, $endDate);
    return date('Y-m-d', $randomTimestamp);
}
