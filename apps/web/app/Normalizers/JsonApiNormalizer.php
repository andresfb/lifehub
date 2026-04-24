<?php

declare(strict_types=1);

namespace App\Normalizers;

use Spatie\LaravelData\Normalizers\Normalizer;

/**
 * Used the Spatie to normalize the JSON:API response.
 */
final class JsonApiNormalizer implements Normalizer
{
    public function normalize(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        if (! array_key_exists('data', $value)) {
            return $this->withAttributes($value);
        }

        $data = $value['data'];

        return $this->withAttributes($data);
    }

    private function withAttributes(array $data): array
    {
        if (! array_key_exists('attributes', $data)) {
            return $data;
        }

        $value = [];

        if (array_key_exists('id', $data)) {
            $value['id'] = $data['id'];
        }

        foreach ($data['attributes'] as $key => $info) {
            $value[$key] = $info;
        }

        return $value;
    }
}
