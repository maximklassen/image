<?php
declare(strict_types=1);

namespace MaximKlassen\Image;

use GdImage;
use MaximKlassen\Image\Contracts\ImageInterface;
use MaximKlassen\Image\Value\Format;
use MaximKlassen\Image\Value\Metadata;

final class Image implements ImageInterface
{
    public function __construct(
        private readonly GdImage $gd,
        private readonly Format $format,
        private readonly Metadata $metadata
    ) {}

    public function getGd(): GdImage { return $this->gd; }

    public function getWidth(): int { return imagesx($this->gd); }
    public function getHeight(): int { return imagesy($this->gd); }
    public function getFormat(): Format { return $this->format; }
    public function getMetadata(): Metadata { return $this->metadata; }

    /** Clone underlying GD resource */
    public function withGd(GdImage $new): self
    {
        return new self($new, $this->format, $this->metadata);
    }

    public function withFormat(Format $format): self
    {
        return new self($this->gd, $format, $this->metadata);
    }

    public function withMetadata(Metadata $metadata): self
    {
        return new self($this->gd, $this->format, $metadata);
    }

    public function toBlob(?Format $format = null, array $options = []): string
    {
        $fmt = $format ?? $this->format;
        $mime = $fmt->mime;
        ob_start();
        switch ($mime) {
            case 'image/jpeg':
                $quality = (int)($options['quality'] ?? 85);
                imagejpeg($this->gd, null, $quality);
                break;
            case 'image/png':
                $compression = (int)($options['compression'] ?? 6); // 0..9
                imagepng($this->gd, null, $compression);
                break;
            case 'image/gif':
                imagegif($this->gd);
                break;
            case 'image/webp':
                $quality = (int)($options['quality'] ?? 80);
                if (!function_exists('imagewebp')) {
                    throw new \RuntimeException('WEBP not supported by GD build.');
                }
                imagewebp($this->gd, null, $quality);
                break;
            default:
                ob_end_clean();
                throw new \RuntimeException("Unsupported encode mime: {$mime}");
        }
        return (string)ob_get_clean();
    }

    public static function fromGd(GdImage $gd, string $mime, ?string $sourceId = null, ?array $exif = null): self
    {
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'bin',
        };
        return new self($gd, new Format($mime, $ext), new Metadata($sourceId, $exif));
    }
}
