<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Value;
final class Format { public function __construct(public readonly string $mime, public readonly string $extension) {} }
