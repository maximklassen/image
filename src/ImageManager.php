<?php
declare(strict_types=1);

namespace MaximKlassen\Image;

use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Drivers\GDDriver;
use MaximKlassen\Image\Support\LocalFilesystem;
use MaximKlassen\Image\Contracts\FilesystemInterface;
use MaximKlassen\Image\Value\Format;

final class ImageManager
{
    public function __construct(
        private readonly DriverInterface $driver,
        private readonly FilesystemInterface $fs = new LocalFilesystem(),
    ) {}

    public static function withDefaults(): self
    {
        return new self(new GDDriver(), new LocalFilesystem());
    }

    public function getDriver(): DriverInterface { return $this->driver; }

    public function readFromPath(string $path): Image
    {
        $blob = $this->fs->read($path);
        $sourceId = realpath($path);
        if ($sourceId) {
            $sourceId .= '|'.(@filemtime($sourceId) ?: '0');
        }
        return $this->driver->decode($blob, $sourceId);
    }

    public function readFromBlob(string $blob, ?string $sourceId = null): Image
    {
        return $this->driver->decode($blob, $sourceId);
    }

    public function writeToPath(Image $img, string $path, ?string $format = null, array $options = []): void
    {
        $fmt = $format ? $this->formatFromString($format) : null;
        $blob = $this->driver->encode($img, $fmt, $options);
        $this->fs->write($path, $blob);
    }

    private function formatFromString(string $extOrMime): Format
    {
        $mime = str_contains($extOrMime, '/') ? $extOrMime : match (strtolower($extOrMime)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => throw new \InvalidArgumentException('Unknown format: '.$extOrMime),
        };
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'bin',
        };
        return new Format($mime, $ext);
    }
}
