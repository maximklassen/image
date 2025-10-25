<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Support;
final class Exif {
    public static function read(string $blob): ?array {
        if (!\function_exists('exif_read_data')) return null;
        $tmp = tmpfile(); if ($tmp===false) return null;
        $meta = stream_get_meta_data($tmp); $path = $meta['uri'] ?? null; if (!$path){ fclose($tmp); return null; }
        fwrite($tmp, $blob); fflush($tmp);
        $exif = @exif_read_data($path, null, true) ?: null; fclose($tmp);
        return is_array($exif) ? $exif : null;
    }
    public static function orientation(?array $exif): int {
        if (!$exif) return 1;
        $o = $exif['IFD0']['Orientation'] ?? ($exif['Orientation'] ?? 1);
        return is_numeric($o) ? (int)$o : 1;
    }
}
