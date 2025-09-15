<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;

final class Crop implements OperationInterface
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $width,
        public readonly int $height
    ) {}

    public function applyWith(DriverInterface $driver, Image $img): Image
    {
        if (method_exists($driver, 'crop')) {
            /** @var callable $fn */
            $fn = [$driver, 'crop'];
            return $fn($img, $this->x, $this->y, $this->width, $this->height);
        }
        return $img;
    }
}
