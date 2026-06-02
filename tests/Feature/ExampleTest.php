<?php

test('home redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
