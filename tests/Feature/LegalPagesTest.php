<?php

test('the terms page is publicly accessible', function () {
    $response = $this->get(route('terms'));

    $response->assertOk();
    $response->assertSee('Terms of Service');
});

test('the privacy page is publicly accessible', function () {
    $response = $this->get(route('privacy'));

    $response->assertOk();
    $response->assertSee('Privacy Policy');
});

test('the landing page footer links to terms and privacy', function () {
    $response = $this->get(route('landing'));

    $response->assertSeeHtml(route('terms'));
    $response->assertSeeHtml(route('privacy'));
});
