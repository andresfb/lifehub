<?php

declare(strict_types=1);

test('the home page redirects to the dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirectToRoute('dashboard');
});
