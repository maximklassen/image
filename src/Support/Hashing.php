<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Support;

final class Hashing
{
    /** Canonical JSON for operations to include in cache keys later */
    public static function canonicalJson(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
    }
}
