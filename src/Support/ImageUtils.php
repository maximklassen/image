<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Support;
final class ImageUtils {
    public static function probe(string $blob): array {
        $info = getimagesizefromstring($blob);
        if ($info === false) throw new \RuntimeException('Unsupported or invalid image data.');
        return ['width'=>$info[0],'height'=>$info[1],'mime'=>$info['mime'] ?? 'application/octet-stream'];
    }
    public static function extFromMime(string $mime): string {
        return match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp', default=>'bin'};
    }
}
