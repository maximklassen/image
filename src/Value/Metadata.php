<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Value;
final class Metadata { public function __construct(public readonly ?string $sourceId, public readonly ?array $exif = null) {} }
