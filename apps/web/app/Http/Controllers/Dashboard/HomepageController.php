<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class HomepageController extends Controller
{
    /**
     * @throws Throwable
     */
    public function show(Request $request): View
    {
        $bookmarks = $this->bookmarks();
        $searchEngines = $this->searchEngines();
        $modules = $this->getNavigation($request->user());

        return view(
            'dashboard.homepage.show',
            [
                'bookmarks' => $bookmarks,
                'searchEngines' => $searchEngines,
                'modules' => $modules,
            ],
        );
    }

    /** @return array<int, array{category: string, items: array<int, array{title: string, icon: string, iconBgClass: string, url: string}>}> */
    private function bookmarks(): array
    {
        return [
            ['category' => 'Social', 'items' => [
                ['title' => 'YouTube',    'icon' => '▶', 'iconBgClass' => 'bg-[#FF0000]', 'url' => 'https://youtube.com'],
                ['title' => 'Reddit',     'icon' => '◉', 'iconBgClass' => 'bg-[#FF4500]', 'url' => 'https://reddit.com'],
                ['title' => 'X / Twitter', 'icon' => '𝕏', 'iconBgClass' => 'bg-[#1a1a1a]', 'url' => 'https://x.com'],
                ['title' => 'Instagram',  'icon' => '◎', 'iconBgClass' => 'bg-[#E1306C]', 'url' => 'https://instagram.com'],
                ['title' => 'LinkedIn',   'icon' => 'in', 'iconBgClass' => 'bg-[#0A66C2]', 'url' => 'https://linkedin.com'],
                ['title' => 'Discord',    'icon' => '◆', 'iconBgClass' => 'bg-[#5865F2]', 'url' => 'https://discord.com'],
            ]],
            ['category' => 'Productivity', 'items' => [
                ['title' => 'Gmail',        'icon' => '✉', 'iconBgClass' => 'bg-[#EA4335]', 'url' => 'https://mail.google.com'],
                ['title' => 'Google Drive', 'icon' => '△', 'iconBgClass' => 'bg-[#4285F4]', 'url' => 'https://drive.google.com'],
                ['title' => 'Notion',       'icon' => 'N', 'iconBgClass' => 'bg-[#000000]', 'url' => 'https://notion.so'],
                ['title' => 'GitHub',       'icon' => '⬡', 'iconBgClass' => 'bg-[#24292e]', 'url' => 'https://github.com'],
                ['title' => 'Figma',        'icon' => '◐', 'iconBgClass' => 'bg-[#A259FF]', 'url' => 'https://figma.com'],
                ['title' => 'Calendar',     'icon' => '▦', 'iconBgClass' => 'bg-[#4285F4]', 'url' => 'https://calendar.google.com'],
            ]],
            ['category' => 'News & Reading', 'items' => [
                ['title' => 'Hacker News', 'icon' => 'Y', 'iconBgClass' => 'bg-[#FF6600]', 'url' => 'https://news.ycombinator.com'],
                ['title' => 'TechCrunch',  'icon' => 'T', 'iconBgClass' => 'bg-[#0A9E01]', 'url' => 'https://techcrunch.com'],
                ['title' => 'The Verge',   'icon' => '▽', 'iconBgClass' => 'bg-[#E2127A]', 'url' => 'https://theverge.com'],
                ['title' => 'Ars Technica', 'icon' => 'A', 'iconBgClass' => 'bg-[#FF6633]', 'url' => 'https://arstechnica.com'],
                ['title' => 'Medium',      'icon' => 'M', 'iconBgClass' => 'bg-[#000000]', 'url' => 'https://medium.com'],
                ['title' => 'Pocket',      'icon' => '◗', 'iconBgClass' => 'bg-[#EF4056]', 'url' => 'https://getpocket.com'],
            ]],
            ['category' => 'Development', 'items' => [
                ['title' => 'Stack Overflow', 'icon' => '▧', 'iconBgClass' => 'bg-[#F48024]', 'url' => 'https://stackoverflow.com'],
                ['title' => 'MDN Docs',       'icon' => 'M', 'iconBgClass' => 'bg-[#1B1B1B]', 'url' => 'https://developer.mozilla.org'],
                ['title' => 'NPM',            'icon' => '▬', 'iconBgClass' => 'bg-[#CB3837]', 'url' => 'https://npmjs.com'],
                ['title' => 'Can I Use',      'icon' => '?', 'iconBgClass' => 'bg-[#5B3B8C]', 'url' => 'https://caniuse.com'],
                ['title' => 'CodePen',        'icon' => '⬡', 'iconBgClass' => 'bg-[#1E1F26]', 'url' => 'https://codepen.io'],
                ['title' => 'Vercel',         'icon' => '▲', 'iconBgClass' => 'bg-[#000000]', 'url' => 'https://vercel.com'],
            ]],
            ['category' => 'Entertainment', 'items' => [
                ['title' => 'Netflix', 'icon' => 'N', 'iconBgClass' => 'bg-[#E50914]', 'url' => 'https://netflix.com'],
                ['title' => 'Spotify', 'icon' => '●', 'iconBgClass' => 'bg-[#1DB954]', 'url' => 'https://spotify.com'],
                ['title' => 'Twitch',  'icon' => '◆', 'iconBgClass' => 'bg-[#9146FF]', 'url' => 'https://twitch.tv'],
                ['title' => 'Steam',   'icon' => '◉', 'iconBgClass' => 'bg-[#1b2838]', 'url' => 'https://store.steampowered.com'],
                ['title' => 'Emby',    'icon' => '▶', 'iconBgClass' => 'bg-[#4CAF50]', 'url' => 'https://emby.media'],
                ['title' => 'Plex',    'icon' => '▶', 'iconBgClass' => 'bg-[#E5A00D]', 'url' => 'https://plex.tv'],
            ]],
            ['category' => 'Utilities', 'items' => [
                ['title' => 'Google Maps', 'icon' => '◎', 'iconBgClass' => 'bg-[#34A853]', 'url' => 'https://maps.google.com'],
                ['title' => 'Translate',   'icon' => '文', 'iconBgClass' => 'bg-[#4285F4]', 'url' => 'https://translate.google.com'],
                ['title' => 'Weather',     'icon' => '☀', 'iconBgClass' => 'bg-[#FFB300]', 'url' => 'https://weather.com'],
                ['title' => 'Speed Test',  'icon' => '⚡', 'iconBgClass' => 'bg-[#6C3BAA]', 'url' => 'https://speedtest.net'],
                ['title' => 'Wolfram',     'icon' => 'W', 'iconBgClass' => 'bg-[#DD1100]', 'url' => 'https://wolframalpha.com'],
                ['title' => 'Archive.org', 'icon' => '∞', 'iconBgClass' => 'bg-[#333333]', 'url' => 'https://archive.org'],
            ]],
        ];
    }

    /** @return array<int, array{name: string, url: string, icon: string, colorClass: string}> */
    private function searchEngines(): array
    {
        return [
            ['name' => 'DuckDuckGo', 'url' => 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=', 'icon' => 'D', 'colorClass' => 'bg-[#DE5833]'],
            ['name' => 'Google',     'url' => 'https://www.google.com/search?q=',             'icon' => 'G', 'colorClass' => 'bg-[#4285F4]'],
            ['name' => 'Bing',       'url' => 'https://www.bing.com/search?q=',               'icon' => 'B', 'colorClass' => 'bg-[#008373]'],
            ['name' => 'Brave',      'url' => 'https://search.brave.com/search?q=',           'icon' => '🦁', 'colorClass' => 'bg-[#FB542B]'],
            ['name' => 'YouTube',    'url' => 'https://www.youtube.com/results?search_query=', 'icon' => '▶', 'colorClass' => 'bg-[#FF0000]'],
            ['name' => 'Reddit',     'url' => 'https://www.reddit.com/search/?q=',            'icon' => '◉', 'colorClass' => 'bg-[#FF4500]'],
        ];
    }
}
