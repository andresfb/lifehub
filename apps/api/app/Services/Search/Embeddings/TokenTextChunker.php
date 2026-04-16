<?php

declare(strict_types=1);

namespace App\Services\Search\Embeddings;

use App\Contracts\Search\TokenTextChunkerInterface;
use Throwable;
use Yethee\Tiktoken\Encoder;
use Yethee\Tiktoken\EncoderProvider;

final readonly class TokenTextChunker implements TokenTextChunkerInterface
{
    public function __construct(private EncoderProvider $encoderProvider) {}

    /**
     * @return array<int, string>
     */
    public function chunk(string $text, string $model = 'text-embedding-3-small'): array
    {
        $text = $this->normalize($text);

        if ($text === '') {
            return [];
        }

        $targetTokens = max(64, $this->integerConfig('search.hybrid.target_tokens', 512));
        $overlapTokens = min(max(0, $this->integerConfig('search.hybrid.overlap_tokens', 64)), $targetTokens - 1);

        try {
            $encoder = $this->encoderProvider->getForModel($model === '' ? 'text-embedding-3-small' : $model);

            return $this->chunkByTokens($text, $encoder, $targetTokens, $overlapTokens);
        } catch (Throwable) {
            return $this->chunkByCharacters($text, $targetTokens * 4, $overlapTokens * 4);
        }
    }

    /**
     * @return array<int, string>
     */
    private function chunkByTokens(string $text, Encoder $encoder, int $targetTokens, int $overlapTokens): array
    {
        $paragraphs = $this->splitParagraphs($text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = $this->trimText($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $paragraphTokens = count($encoder->encode($paragraph));

            if ($paragraphTokens > $targetTokens) {
                if ($current !== '') {
                    $chunks[] = $current;
                    $current = '';
                }

                array_push($chunks, ...$this->chunkByCharacters($paragraph, $targetTokens * 4, $overlapTokens * 4));

                continue;
            }

            $separator = $current === '' ? '' : "\n\n";
            $candidate = $current.$separator.$paragraph;
            $candidateTokens = count($encoder->encode($candidate));

            if ($candidateTokens <= $targetTokens) {
                $current = $candidate;

                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            $current = $this->withOverlap($current, $paragraph, $overlapTokens * 4, $targetTokens * 4);
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        $filtered = [];

        foreach ($chunks as $chunk) {
            $chunk = $this->trimText($chunk);

            if ($chunk !== '') {
                $filtered[] = $chunk;
            }
        }

        return $filtered;
    }

    /**
     * @return array<int, string>
     */
    private function chunkByCharacters(string $text, int $targetChars, int $overlapChars): array
    {
        /** @var array<int, string> $chunks */
        $chunks = [];
        $length = mb_strlen($text);
        $start = 0;
        $step = max(1, $targetChars - $overlapChars);

        while ($start < $length) {
            $piece = mb_substr($text, $start, $targetChars);
            $chunk = $this->trimText($piece);

            if ($chunk !== '') {
                $chunks[] = $chunk;
            }

            $start += $step;
        }

        return $chunks;
    }

    private function withOverlap(string $previousChunk, string $nextSegment, int $overlapChars, int $targetChars): string
    {
        $tail = mb_substr($previousChunk, max(0, mb_strlen($previousChunk) - $overlapChars));
        $candidate = $this->trimText($tail."\n\n".$nextSegment);

        if (mb_strlen($candidate) <= $targetChars) {
            return $candidate;
        }

        $limited = mb_substr($candidate, 0, $targetChars);

        return $this->trimText($limited);
    }

    private function normalize(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $normalized = preg_replace('/[ \t]+/u', ' ', $text);
        if (is_string($normalized)) {
            $text = $normalized;
        }

        $normalized = preg_replace("/\n{3,}/u", "\n\n", $text);
        if (is_string($normalized)) {
            $text = $normalized;
        }

        return $this->trimText($text);
    }

    private function integerConfig(string $key, int $default): int
    {
        $value = config($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function trimText(string $value): string
    {
        $trimmed = preg_replace('/^\s+|\s+$/u', '', $value);

        return is_string($trimmed) ? $trimmed : $value;
    }

    /**
     * @return array<int, string>
     */
    private function splitParagraphs(string $text): array
    {
        $parts = preg_split("/\n{2,}/u", $text);

        if (! is_array($parts)) {
            return [];
        }

        /** @var array<int, string> $paragraphs */
        $paragraphs = [];

        foreach ($parts as $part) {
            if (is_string($part)) {
                $paragraphs[] = $part;
            }
        }

        return $paragraphs;
    }
}
