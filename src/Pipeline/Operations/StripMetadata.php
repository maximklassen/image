<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Pipeline\Operations;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;
use MaximKlassen\Image\Value\Metadata;
final class StripMetadata implements OperationInterface {
    public function applyWith(DriverInterface $driver, Image $img): Image {
        $meta = $img->getMetadata(); $clean = new Metadata($meta->sourceId, null);
        if (method_exists($img, 'withMetadata')) { return $img->withMetadata($clean); }
        return $img;
    }
    public function toArray(): array { return ['op'=>'strip_metadata']; }
}
