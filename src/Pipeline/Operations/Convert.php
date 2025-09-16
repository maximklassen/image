<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;
use MaximKlassen\Image\Value\Format;

final class Convert implements OperationInterface
{
    public function __construct(
        public readonly Format $format
    ) {}

    public function applyWith(DriverInterface $driver, Image $img): Image
    {
        if (method_exists($img, 'withFormat')) {
            return $img->withFormat($this->format);
        }
        return $img;
    }
}
