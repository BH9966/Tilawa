<?php

test('the homepage shows the public landing page', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Get Started');
});
