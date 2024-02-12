<?php

it('has article/bulkarticle page', function () {
    $response = $this->get('/article/bulkarticle');

    $response->assertStatus(200);
});
