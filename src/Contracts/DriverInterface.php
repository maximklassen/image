<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Contracts;

use MaximKlassen\Image\Image;
use MaximKlassen\Image\Value\Format;
use MaximKlassen\Image\Pipeline\Operations\OperationInterface;

interface DriverInterface
{
    /** Decode from binary blob into immutable Image */
    public function decode(string $blob, ?string $sourceId = null): Image;

    /** Encode immutable Image to binary blob */
    public function encode(Image $img, ?Format $format = null, array $options = []): string;

    /** Apply single operation immutably and return new Image */
    public function apply(Image $img, OperationInterface $op): Image;

    public function getName(): string; // e.g. "gd"
}
