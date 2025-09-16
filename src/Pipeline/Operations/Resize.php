<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;

final class Resize implements OperationInterface
{
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {}

    public function applyWith(DriverInterface $driver, Image $img): Image
    {
        if (method_exists($driver, 'resize')) {
            /** @var callable $fn */
            $fn = [$driver, 'resize'];
            return $fn($img, $this->width, $this->height);
        }
        return $img;
    }
}
