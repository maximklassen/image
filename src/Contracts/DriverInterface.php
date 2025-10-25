<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Contracts;
use MaximKlassen\Image\Image;
use MaximKlassen\Image\Value\Format;
use MaximKlassen\Image\Pipeline\Operations\OperationInterface;
interface DriverInterface {
    public function decode(string $blob, ?string $sourceId = null): Image;
    public function encode(Image $img, ?Format $format = null, array $options = []): string;
    public function apply(Image $img, OperationInterface $op): Image;
    public function getName(): string;
}
