<?php

it('has user\followers page', function () {
    $response = $this->get('/user\followers');

    $response->assertStatus(200);
});
