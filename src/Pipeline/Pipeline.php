<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Pipeline;

use MaximKlassen\Image\Image;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Pipeline\Operations\OperationInterface;
use MaximKlassen\Image\Pipeline\Operations\AutoOrient;
use MaximKlassen\Image\Pipeline\Operations\Crop;
use MaximKlassen\Image\Pipeline\Operations\Fit;

final class Pipeline
{
    /** @var OperationInterface[] */
    private array $ops = [];
    private function __construct(private readonly Image $image) {}

    public static function from(Image $img): self
    {
        return new self($img);
    }

    public static function of(Image $img, array $ops): self
    {
        $p = new self($img);
        foreach ($ops as $op) $p->ops[] = $op;
        return $p;
    }

    public function autoOrient(): self
    {
        $this->ops[] = new AutoOrient();
        return self::from($this->applyInline(new class($this->ops) {
            public function __construct(public array &$ops) {}
        })); // no-op, just fluent. Kept for chaining style consistency.
    }

    public function crop(int $x, int $y, int $w, int $h): self
    {
        $this->ops[] = new Crop($x,$y,$w,$h);
        return $this;
    }

    /** @param array{0:string,1:string}|array{string,string} $position */
    public function fit(int $width, int $height, string $mode = 'cover', array $position = ['center','center']): self
    {
        $this->ops[] = new Fit($width, $height, $mode, $position[0] ?? 'center', $position[1] ?? 'center');
        return $this;
    }

    public function apply(DriverInterface $driver): Image
    {
        $img = $this->image;
        foreach ($this->ops as $op) {
            $img = $driver->apply($img, $op);
        }
        return $img;
    }

    // Internal helper to keep fluent API stable without eager apply
    private function applyInline($dummy): Image
    {
        return $this->image;
    }
}
