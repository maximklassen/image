<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;

final class Fit implements OperationInterface
{
    public function __construct(
        public readonly int $width,
        public readonly int $height,
        public readonly string $mode = 'cover', // cover|contain
        public readonly string $horizontal = 'center', // left|center|right
        public readonly string $vertical = 'center' // top|center|bottom
    ) {}

    public function applyWith(DriverInterface $driver, Image $img): Image
    {
        if (method_exists($driver, 'fit')) {
            /** @var callable $fn */
            $fn = [$driver, 'fit'];
            return $fn($img, $this->width, $this->height, $this->mode, $this->horizontal, $this->vertical);
        }
        return $img;
    }
}
