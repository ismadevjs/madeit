<?php


it('returns an empty array when there are no providers', function () {
    $response = $this->get("/api/providers");

    $response->assertStatus(200);
    $response->assertExactJson([]);
});
