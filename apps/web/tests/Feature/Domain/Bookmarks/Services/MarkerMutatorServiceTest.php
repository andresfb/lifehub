<?php

declare(strict_types=1);

use App\Domain\Bookmarks\Services\MarkerMutatorService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

beforeEach(function () {
    $this->service = new MarkerMutatorService;
});

test('extractMeta returns title and description via HTTP when successful', function () {
    Http::fake([
        'example.com/*' => Http::response('<html><head><title>Example Title</title><meta property="og:description" content="Example description"></head></html>'),
    ]);

    Config::set('markers.browsershot_fallback', false);

    [$title, $description] = $this->service->extractMeta('https://example.com/page');

    expect($title)->toBe('Example Title')
        ->and($description)->toBe('Example description');
});

test('extractMeta falls back to meta description when og:description is missing', function () {
    Http::fake([
        'example.com/*' => Http::response('<html><head><title>Title</title><meta name="description" content="Meta desc"></head></html>'),
    ]);

    [$title, $description] = $this->service->extractMeta('https://example.com/page');

    expect($title)->toBe('Title')
        ->and($description)->toBe('Meta desc');
});

test('extractMeta returns nulls when HTTP fails and browsershot fallback is disabled', function () {
    Http::fake([
        'example.com/*' => Http::response('Forbidden', 403),
    ]);

    Config::set('markers.browsershot_fallback', false);

    Log::shouldReceive('error')->once();

    [$title, $description] = $this->service->extractMeta('https://example.com/blocked');

    expect($title)->toBeNull()
        ->and($description)->toBeNull();
});

test('extractMeta falls back to browsershot when HTTP returns empty and fallback is enabled', function () {
    Http::fake([
        'example.com/*' => Http::response(''),
    ]);

    Config::set('markers.browsershot_fallback', true);

    $html = '<html><head><title>Browser Title</title><meta property="og:description" content="Browser desc"></head></html>';

    $mock = Mockery::mock('overload:'.Browsershot::class);
    $mock->shouldReceive('url')->once()->andReturnSelf();
    $mock->shouldReceive('userAgent')->once()->andReturnSelf();
    $mock->shouldReceive('timeout')->once()->andReturnSelf();
    $mock->shouldReceive('noSandbox')->once()->andReturnSelf();
    $mock->shouldReceive('dismissDialogs')->once()->andReturnSelf();
    $mock->shouldReceive('setOption')->once()->andReturnSelf();
    $mock->shouldReceive('bodyHtml')->once()->andReturn($html);

    Log::shouldReceive('info')->once();

    $service = new MarkerMutatorService;
    [$title, $description] = $service->extractMeta('https://example.com/blocked');

    expect($title)->toBe('Browser Title')
        ->and($description)->toBe('Browser desc');
});

test('extractMeta returns nulls gracefully when both HTTP and browsershot fail', function () {
    Http::fake([
        'example.com/*' => Http::response(''),
    ]);

    Config::set('markers.browsershot_fallback', true);

    $mock = Mockery::mock('overload:'.Browsershot::class);
    $mock->shouldReceive('url')->once()->andReturnSelf();
    $mock->shouldReceive('userAgent')->once()->andReturnSelf();
    $mock->shouldReceive('timeout')->once()->andReturnSelf();
    $mock->shouldReceive('noSandbox')->once()->andReturnSelf();
    $mock->shouldReceive('dismissDialogs')->once()->andReturnSelf();
    $mock->shouldReceive('setOption')->once()->andReturnSelf();
    $mock->shouldReceive('bodyHtml')->once()->andThrow(new RuntimeException('Chrome not found'));

    Log::shouldReceive('info')->once();
    Log::shouldReceive('error')->once();

    $service = new MarkerMutatorService;
    [$title, $description] = $service->extractMeta('https://example.com/broken');

    expect($title)->toBeNull()
        ->and($description)->toBeNull();
});

test('getDomain extracts domain from URL', function () {
    expect($this->service->getDomain('https://www.imdb.com/title/tt123'))
        ->toBe('imdb.com')
        ->and($this->service->getDomain('https://example.co.uk/page'))
        ->toBe('example.co.uk')
        ->and($this->service->getDomain(''))
        ->toBe('');
});
