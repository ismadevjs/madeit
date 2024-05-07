<?php


it('check if the provider api is 200', function () {
    $response = $this->get("/api/providers");
    $response->assertStatus(200);
});
