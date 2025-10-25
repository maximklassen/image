<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Pipeline\Operations;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;
final class AutoOrient implements OperationInterface {
    public function applyWith(DriverInterface $driver, Image $img): Image {
        if (method_exists($driver, 'autoOrient')) { $fn = [$driver, 'autoOrient']; return $fn($img); }
        return $img;
    }
    public function toArray(): array { return ['op'=>'auto_orient']; }
}
