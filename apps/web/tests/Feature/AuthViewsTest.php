<?php

declare(strict_types=1);

use Illuminate\Support\ViewErrorBag;

test('auth layout sets the daisyui theme before assets load', function () {
    $this->withoutVite();

    $html = (string) $this->view('auth.login.show', [
        'errors' => new ViewErrorBag(),
    ]);

    expect($html)
        ->toContain("document.documentElement.dataset.theme = localStorage.getItem('lh_theme') === 'dark' ? 'forest' : 'emerald';")
        ->toContain('class="card border border-base-300 bg-base-100 shadow-xl"')
        ->toContain('class="btn btn-primary w-full"');
});

test('two factor view renders daisyui form controls and state classes', function () {
    $this->withoutVite();
    session(['tfa-ttl' => 90]);

    $html = (string) $this->view('auth.two-factor.show', [
        'errors' => new ViewErrorBag(),
    ]);

    expect($html)
        ->toContain('input input-bordered w-full text-center font-display font-bold tracking-[0.4em]')
        ->toContain("x-bind:class=\"{ 'text-error': hasExpired, 'text-base-content': ! hasExpired }\"")
        ->toContain('btn btn-primary w-full disabled:btn-disabled');
});
