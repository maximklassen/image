<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Value;

final class Metadata
{
    /** @param array<string,mixed> $exif */
    public function __construct(
        public readonly ?string $sourceId,
        public readonly ?array $exif = null,
    ) {}
}
