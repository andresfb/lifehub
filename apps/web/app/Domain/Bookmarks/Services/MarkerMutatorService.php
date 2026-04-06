<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Services;

use App\Domain\Bookmarks\Models\Marker;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

final class MarkerMutatorService
{
    public function execute(Marker $marker): void
    {
        try {
            [$title, $description] = $this->extractMeta($marker->url);

            if (blank($marker->title)) {
                $marker->title = $title;
            } else {
                $description = str($title ?? '')
                    ->newLine(2)
                    ->append($description ?? '')
                    ->trim()
                    ->toString();

                if ($description === '') {
                    $description = null;
                }
            }

            $marker->description = $description;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $marker->domain = $marker->domain ?: $this->getDomain($marker->url);
        $marker->saveQuietly();
        Cache::tags('markers')->flush();

        // TODO: change this to a job
        $this->captureScreenshot($marker);
    }

    /**
     * @return array{0: ?string, 1: ?string}
     *
     * @throws Exception
     */
    public function extractMeta(string $url): array
    {
        [$title, $description] = $this->extractMetaViaHttp($url);

        if (blank($title) && blank($description) && Config::boolean('markers.browsershot_fallback')) {
            Log::info("Falling back to headless browser for: {$url}");
            [$title, $description] = $this->extractMetaViaBrowser($url);
        }

        return [$title, $description];
    }

    public function getDomain(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $pieces = parse_url($url);
        $domain = $pieces['host'] ?? $pieces['path'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return '';
    }

    /**
     * @return array{0: ?string, 1: ?string}
     *
     * @throws Exception
     */
    private function extractMetaViaHttp(string $url): array
    {
        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => Config::string('constants.crawler_agent'),
            ])
            ->get($url);

        if (! $response->successful()) {
            Log::error(
                sprintf("Error getting meta data for url '%s'. Status: %s. Response: %s",
                    $url,
                    $response->status(),
                    $response->body()
                )
            );

            return [null, null];
        }

        $html = $response->body();
        if (blank($html)) {
            return [null, null];
        }

        return $this->parseHtml($html);
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function extractMetaViaBrowser(string $url): array
    {
        try {
            $html = Browsershot::url($url)
                ->userAgent(Config::string('constants.crawler_agent'))
                ->timeout(Config::integer('markers.browsershot_timeout'))
                ->noSandbox()
                ->dismissDialogs()
                ->setOption('waitUntil', 'domcontentloaded')
                ->bodyHtml();

            if (blank($html)) {
                return [null, null];
            }

            return $this->parseHtml($html);
        } catch (Exception $e) {
            Log::error("Browsershot extraction failed for {$url}: {$e->getMessage()}");

            return [null, null];
        }
    }

    private function captureScreenshot(Marker $marker): void
    {
        // TODO: move this to a core level service so other Modules can use it and adapt the config values

        if (! Config::boolean('markers.browsershot_fallback')) {
            return;
        }

        if ($marker->hasMedia('screenshot')) {
            return;
        }

        try {
            $directory = storage_path('app/private/screenshots');
            File::ensureDirectoryExists($directory);

            $tempPath = "{$directory}/{$marker->id}.png";

            Browsershot::url($marker->url)
                ->userAgent(Config::string('constants.crawler_agent'))
                ->timeout(Config::integer('markers.browsershot_timeout'))
                ->noSandbox()
                ->dismissDialogs()
                ->setOption('waitUntil', 'domcontentloaded')
                ->windowSize(1280, 800)
                ->save($tempPath);

            $marker->addMedia($tempPath)
                ->toMediaCollection('screenshot');
        } catch (Exception $e) {
            Log::error("Screenshot capture failed for {$marker->url}: {$e->getMessage()}");
        }
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function parseHtml(string $html): array
    {
        $crawler = new Crawler($html);
        $title = $crawler->filter('title')->count() !== 0
            ? trim($crawler->filter('title')->text())
            : null;

        // Prefer OG description, fallback to meta description
        if ($crawler->filter('meta[property="og:description"]')->count() !== 0) {
            $description = $crawler->filter('meta[property="og:description"]')->attr('content');
        } else {
            $description = $crawler->filter('meta[name="description"]')->count() !== 0
                ? $crawler->filter('meta[name="description"]')->attr('content')
                : null;
        }

        return [$title, $description];
    }
}
