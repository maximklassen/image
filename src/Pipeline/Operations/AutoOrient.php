<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;

final class AutoOrient implements OperationInterface
{
    public function applyWith(DriverInterface $driver, Image $img): Image
    {
        // Delegate to driver-specific implementation
        if (method_exists($driver, 'autoOrient')) {
            /** @var callable $fn */
            $fn = [$driver, 'autoOrient'];
            return $fn($img);
        }
        return $img;
    }
}
