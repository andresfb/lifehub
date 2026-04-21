<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function show(): View
    {
        $bookmarks = $this->bookmarks();
        $searchEngines = $this->searchEngines();

        return view('dashboard.homepage.show', compact('bookmarks', 'searchEngines'));
    }

    /** @return array<int, array{category: string, items: array<int, array{title: string, icon: string, iconBg: string, url: string}>}> */
    private function bookmarks(): array
    {
        return [
            ['category' => 'Social', 'items' => [
                ['title' => 'YouTube',    'icon' => '▶', 'iconBg' => '#FF0000', 'url' => 'https://youtube.com'],
                ['title' => 'Reddit',     'icon' => '◉', 'iconBg' => '#FF4500', 'url' => 'https://reddit.com'],
                ['title' => 'X / Twitter', 'icon' => '𝕏', 'iconBg' => '#1a1a1a', 'url' => 'https://x.com'],
                ['title' => 'Instagram',  'icon' => '◎', 'iconBg' => '#E1306C', 'url' => 'https://instagram.com'],
                ['title' => 'LinkedIn',   'icon' => 'in', 'iconBg' => '#0A66C2', 'url' => 'https://linkedin.com'],
                ['title' => 'Discord',    'icon' => '◆', 'iconBg' => '#5865F2', 'url' => 'https://discord.com'],
            ]],
            ['category' => 'Productivity', 'items' => [
                ['title' => 'Gmail',        'icon' => '✉', 'iconBg' => '#EA4335', 'url' => 'https://mail.google.com'],
                ['title' => 'Google Drive', 'icon' => '△', 'iconBg' => '#4285F4', 'url' => 'https://drive.google.com'],
                ['title' => 'Notion',       'icon' => 'N', 'iconBg' => '#000000', 'url' => 'https://notion.so'],
                ['title' => 'GitHub',       'icon' => '⬡', 'iconBg' => '#24292e', 'url' => 'https://github.com'],
                ['title' => 'Figma',        'icon' => '◐', 'iconBg' => '#A259FF', 'url' => 'https://figma.com'],
                ['title' => 'Calendar',     'icon' => '▦', 'iconBg' => '#4285F4', 'url' => 'https://calendar.google.com'],
            ]],
            ['category' => 'News & Reading', 'items' => [
                ['title' => 'Hacker News', 'icon' => 'Y', 'iconBg' => '#FF6600', 'url' => 'https://news.ycombinator.com'],
                ['title' => 'TechCrunch',  'icon' => 'T', 'iconBg' => '#0A9E01', 'url' => 'https://techcrunch.com'],
                ['title' => 'The Verge',   'icon' => '▽', 'iconBg' => '#E2127A', 'url' => 'https://theverge.com'],
                ['title' => 'Ars Technica', 'icon' => 'A', 'iconBg' => '#FF6633', 'url' => 'https://arstechnica.com'],
                ['title' => 'Medium',      'icon' => 'M', 'iconBg' => '#000000', 'url' => 'https://medium.com'],
                ['title' => 'Pocket',      'icon' => '◗', 'iconBg' => '#EF4056', 'url' => 'https://getpocket.com'],
            ]],
            ['category' => 'Development', 'items' => [
                ['title' => 'Stack Overflow', 'icon' => '▧', 'iconBg' => '#F48024', 'url' => 'https://stackoverflow.com'],
                ['title' => 'MDN Docs',       'icon' => 'M', 'iconBg' => '#1B1B1B', 'url' => 'https://developer.mozilla.org'],
                ['title' => 'NPM',            'icon' => '▬', 'iconBg' => '#CB3837', 'url' => 'https://npmjs.com'],
                ['title' => 'Can I Use',      'icon' => '?', 'iconBg' => '#5B3B8C', 'url' => 'https://caniuse.com'],
                ['title' => 'CodePen',        'icon' => '⬡', 'iconBg' => '#1E1F26', 'url' => 'https://codepen.io'],
                ['title' => 'Vercel',         'icon' => '▲', 'iconBg' => '#000000', 'url' => 'https://vercel.com'],
            ]],
            ['category' => 'Entertainment', 'items' => [
                ['title' => 'Netflix', 'icon' => 'N', 'iconBg' => '#E50914', 'url' => 'https://netflix.com'],
                ['title' => 'Spotify', 'icon' => '●', 'iconBg' => '#1DB954', 'url' => 'https://spotify.com'],
                ['title' => 'Twitch',  'icon' => '◆', 'iconBg' => '#9146FF', 'url' => 'https://twitch.tv'],
                ['title' => 'Steam',   'icon' => '◉', 'iconBg' => '#1b2838', 'url' => 'https://store.steampowered.com'],
                ['title' => 'Emby',    'icon' => '▶', 'iconBg' => '#4CAF50', 'url' => 'https://emby.media'],
                ['title' => 'Plex',    'icon' => '▶', 'iconBg' => '#E5A00D', 'url' => 'https://plex.tv'],
            ]],
            ['category' => 'Utilities', 'items' => [
                ['title' => 'Google Maps', 'icon' => '◎', 'iconBg' => '#34A853', 'url' => 'https://maps.google.com'],
                ['title' => 'Translate',   'icon' => '文', 'iconBg' => '#4285F4', 'url' => 'https://translate.google.com'],
                ['title' => 'Weather',     'icon' => '☀', 'iconBg' => '#FFB300', 'url' => 'https://weather.com'],
                ['title' => 'Speed Test',  'icon' => '⚡', 'iconBg' => '#6C3BAA', 'url' => 'https://speedtest.net'],
                ['title' => 'Wolfram',     'icon' => 'W', 'iconBg' => '#DD1100', 'url' => 'https://wolframalpha.com'],
                ['title' => 'Archive.org', 'icon' => '∞', 'iconBg' => '#333333', 'url' => 'https://archive.org'],
            ]],
        ];
    }

    /** @return array<int, array{name: string, url: string, icon: string, color: string}> */
    private function searchEngines(): array
    {
        return [
            ['name' => 'Google',     'url' => 'https://www.google.com/search?q=',             'icon' => 'G', 'color' => '#4285F4'],
            ['name' => 'DuckDuckGo', 'url' => 'https://duckduckgo.com/?q=',                   'icon' => 'D', 'color' => '#DE5833'],
            ['name' => 'Bing',       'url' => 'https://www.bing.com/search?q=',               'icon' => 'B', 'color' => '#008373'],
            ['name' => 'Brave',      'url' => 'https://search.brave.com/search?q=',           'icon' => '🦁', 'color' => '#FB542B'],
            ['name' => 'YouTube',    'url' => 'https://www.youtube.com/results?search_query=', 'icon' => '▶', 'color' => '#FF0000'],
            ['name' => 'Reddit',     'url' => 'https://www.reddit.com/search/?q=',            'icon' => '◉', 'color' => '#FF4500'],
        ];
    }
}
