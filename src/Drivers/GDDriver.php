<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Drivers;

use GdImage;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Image;
use MaximKlassen\Image\Value\Format;
use MaximKlassen\Image\Support\ImageUtils;
use MaximKlassen\Image\Support\Exif;
use MaximKlassen\Image\Pipeline\Operations\OperationInterface;
use MaximKlassen\Image\Pipeline\Operations\AutoOrient;
use MaximKlassen\Image\Pipeline\Operations\Crop;
use MaximKlassen\Image\Pipeline\Operations\Fit;

final class GDDriver implements DriverInterface
{
    public function getName(): string { return 'gd'; }

    public function decode(string $blob, ?string $sourceId = null): Image
    {
        $info = ImageUtils::probe($blob);
        $gd = imagecreatefromstring($blob);
        if (!$gd instanceof GdImage) {
            throw new \RuntimeException('Failed to decode image.');
        }
        $exif = Exif::read($blob);
        return Image::fromGd($gd, $info['mime'], $sourceId, $exif);
    }

    public function encode(Image $img, ?Format $format = null, array $options = []): string
    {
        return $img->toBlob($format, $options);
    }

    public function apply(Image $img, OperationInterface $op): Image
    {
        return $op->applyWith($this, $img);
    }

    // ---------- Primitive operations (used by operations) ----------

    public function autoOrient(Image $img): Image
    {
        $exif = $img->getMetadata()->exif ?? null;
        $orientation = Exif::orientation($exif);
        $gd = $img->getGd();
        $result = $gd;

        switch ($orientation) {
            case 2: // flip horizontal
                imageflip($gd, IMG_FLIP_HORIZONTAL);
                break;
            case 3: // rotate 180
                $result = imagerotate($gd, 180, 0);
                break;
            case 4: // flip vertical
                imageflip($gd, IMG_FLIP_VERTICAL);
                break;
            case 5: // rotate 90 CW + flip horizontal
                $result = imagerotate($gd, -90, 0);
                imageflip($result, IMG_FLIP_HORIZONTAL);
                break;
            case 6: // rotate 90 CW
                $result = imagerotate($gd, -90, 0);
                break;
            case 7: // rotate 90 CCW + flip horizontal
                $result = imagerotate($gd, 90, 0);
                imageflip($result, IMG_FLIP_HORIZONTAL);
                break;
            case 8: // rotate 90 CCW
                $result = imagerotate($gd, 90, 0);
                break;
            case 1:
            default:
                return $img; // no changes
        }
        if (!$result instanceof GdImage) {
            throw new \RuntimeException('Failed to auto-orient image.');
        }
        return $img->withGd($result);
    }

    public function crop(Image $img, int $x, int $y, int $w, int $h): Image
    {
        $src = $img->getGd();
        $cropped = imagecrop($src, ['x' => $x, 'y' => $y, 'width' => $w, 'height' => $h]);
        if (!$cropped instanceof GdImage) {
            throw new \RuntimeException('Failed to crop image.');
        }
        return $img->withGd($cropped);
    }

    /** Resize to exact size (no aspect keep). */
    public function resize(Image $img, int $w, int $h): Image
    {
        $src = $img->getGd();
        $dst = imagecreatetruecolor($w, $h);
        // handle alpha for PNG/WebP
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        if (!imagecopyresampled($dst, $src, 0,0,0,0, $w,$h, imagesx($src), imagesy($src))) {
            throw new \RuntimeException('Failed to resize image.');
        }
        return $img->withGd($dst);
    }

    /** Fit: mode 'cover' (crop to fill) or 'contain' (inside, no crop). */
    public function fit(Image $img, int $tw, int $th, string $mode = 'cover', string $hx='center', string $vy='center'): Image
    {
        $sw = $img->getWidth();
        $sh = $img->getHeight();

        if ($mode === 'contain') {
            $scale = min($tw / $sw, $th / $sh);
            $nw = max(1, (int)round($sw * $scale));
            $nh = max(1, (int)round($sh * $scale));
            return $this->resize($img, $nw, $nh);
        }

        // cover: scale to cover and then crop center (or given position)
        $scale = max($tw / $sw, $th / $sh);
        $nw = max(1, (int)round($sw * $scale));
        $nh = max(1, (int)round($sh * $scale));
        $resized = $this->resize($img, $nw, $nh);

        $ox = match ($hx) {
            'left' => 0,
            'right' => $nw - $tw,
            default => (int) max(0, floor(($nw - $tw) / 2)),
        };
        $oy = match ($vy) {
            'top' => 0,
            'bottom' => $nh - $th,
            default => (int) max(0, floor(($nh - $th) / 2)),
        };
        return $this->crop($resized, $ox, $oy, $tw, $th);
    }
}
