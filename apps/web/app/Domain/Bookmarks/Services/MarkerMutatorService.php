<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Services;

use App\Domain\Bookmarks\Models\Marker;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
    }

    /**
     * @throws Exception
     */
    public function extractMeta(string $url): array
    {
        $response = Http::timeout(10)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0',
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
}
