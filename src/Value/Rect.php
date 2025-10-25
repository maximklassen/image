<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Value;
final class Rect { public function __construct(public readonly int $x, public readonly int $y, public readonly int $width, public readonly int $height){ if ($width<=0||$height<=0) throw new \InvalidArgumentException('Rect size must be positive.'); } }
