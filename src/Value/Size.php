<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Value;
final class Size { public function __construct(public readonly int $width, public readonly int $height) { if ($width<=0||$height<=0) throw new \InvalidArgumentException('Size must be positive.'); } }
