<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline\Operations;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;

interface OperationInterface
{
    public function applyWith(DriverInterface $driver, Image $img): Image;
}
