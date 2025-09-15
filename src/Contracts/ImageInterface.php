<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Contracts;

use MaximKlassen\Image\Value\Format;
use MaximKlassen\Image\Value\Metadata;

interface ImageInterface
{
    public function getWidth(): int;
    public function getHeight(): int;
    public function getFormat(): Format;
    public function getMetadata(): Metadata;

    /** Return binary string (blob). If $format is null, keep original. */
    public function toBlob(?Format $format = null, array $options = []): string;
}
