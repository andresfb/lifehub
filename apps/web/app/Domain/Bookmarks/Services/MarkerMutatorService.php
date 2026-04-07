<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Services;

use App\Domain\Bookmarks\Jobs\MarkerScreenshotJob;
use App\Domain\Bookmarks\Libraries\MediaNamesLibrary;
use App\Domain\Bookmarks\Models\Marker;
use App\Dtos\Media\PageScreenshotItem;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

final class MarkerMutatorService
{
    public function execute(Marker $marker): void
    {
        try {
            if ($this->bandedDomain($marker->url)) {
                Log::notice("{$marker->url} cannot be mutated");
                $this->saveWithDomain($marker);

                return;
            }

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

        $this->saveWithDomain($marker);

        MarkerScreenshotJob::dispatch(
            new PageScreenshotItem(
                modelId: $marker->id,
                url: $marker->url,
                collection: MediaNamesLibrary::screenshot(),
            )
        );
    }

    /**
     * @return array{0: ?string, 1: ?string}
     *
     * @throws Exception
     */
    public function extractMeta(string $url): array
    {
        [$title, $description] = $this->extractMetaViaHttp($url);

        if (blank($title) && blank($description) && Config::boolean('constants.browsershot_fallback')) {
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
                ->timeout(Config::integer('constants.browsershot_timeout'))
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

    private function bandedDomain(string $url): bool
    {
        $banded = Config::array('markers.mutator_banded_domains');
        foreach ($banded as $item) {
            if (str($url)->contains($item, true)) {
                return true;
            }
        }

        return false;
    }

    private function saveWithDomain(Marker $marker): void
    {
        $marker->domain = $marker->domain ?: $this->getDomain($marker->url);
        $marker->saveQuietly();
        Cache::tags('markers')->flush();
    }
}
