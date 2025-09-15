<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Value;

final class Format
{
    public function __construct(
        public readonly string $mime, // e.g. image/jpeg
        public readonly string $extension // e.g. jpg
    ) {}
}
