<?php
declare(strict_types=1);
namespace MaximKlassen\Image;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Drivers\GDDriver;
use MaximKlassen\Image\Support\LocalFilesystem;
use MaximKlassen\Image\Contracts\FilesystemInterface;
use MaximKlassen\Image\Value\Format;
final class ImageManager {
    public function __construct(
        private readonly DriverInterface $driver,
        private readonly FilesystemInterface $fs = new LocalFilesystem(),
        private string $cacheRoot = 'cache',
        private ?string $publicRoot = null,
        private ?string $publicPrefix = null,
    ) {}
    public static function withDefaults(): self { return new self(new GDDriver(), new LocalFilesystem()); }
    public function setCacheRoot(string $dir): void { $this->cacheRoot = $dir; }
    public function setPublicMapping(?string $publicRoot, ?string $publicPrefix): void { $this->publicRoot = $publicRoot; $this->publicPrefix = $publicPrefix; }
    public function getDriver(): DriverInterface { return $this->driver; }
    public function readFromPath(string $path): Image {
        $blob = $this->fs->read($path); $sourceId = realpath($path);
        if ($sourceId) { $sourceId .= '|'.(@filemtime($sourceId) ?: '0'); }
        return $this->driver->decode($blob, $sourceId);
    }
    public function readFromBlob(string $blob, ?string $sourceId = null): Image { return $this->driver->decode($blob, $sourceId); }
    public function writeToPath(Image $img, string $path, ?string $format = null, array $options = []): void {
        $fmt = $format ? $this->formatFromString($format) : null; $blob = $this->driver->encode($img, $fmt, $options); $this->fs->write($path, $blob);
    }
    private function formatFromString(string $extOrMime): Format {
        $mime = str_contains($extOrMime, '/') ? $extOrMime : match (strtolower($extOrMime)) {
            'jpg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp', default=>throw new \InvalidArgumentException('Unknown format: '.$extOrMime),
        };
        $ext = match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp', default=>'bin'};
        return new Format($mime, $ext);
    }
    private function cacheKey(\MaximKlassen\Image\Pipeline\Pipeline $p, string $targetMime, array $options): string {
        $img = $p->getImage();
        $sourceId = $img->getMetadata()->sourceId ?? sha1($this->getDriver()->encode($img, null, []));
        $ops = $p->operationsArray();
        $data = ['v'=>'1','driver'=>$this->getDriver()->getName(),'source'=>$sourceId,'ops'=>$ops,'format'=>$targetMime,'options'=>$options];
        $json = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION);
        return sha1($json);
    }
    private function ensureDir(string $dir): void { if(!is_dir($dir)){ if(!@mkdir($dir,0775,true)&&!is_dir($dir)) throw new \RuntimeException("Cannot create dir: {$dir}"); } }
    public function cache(\MaximKlassen\Image\Pipeline\Pipeline $p, string $targetFormat, array $options = []): string {
        $mime = str_contains($targetFormat, '/') ? $targetFormat : match (strtolower($targetFormat)) {
            'jpg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp', default=>throw new \InvalidArgumentException('Unknown format: '.$targetFormat),
        };
        $ext = match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp', default=>'bin'};
        $key = $this->cacheKey($p, $mime, $options); $prefix = substr($key,0,2);
        $dir = rtrim($this->cacheRoot,'/').'/'.$prefix; $this->ensureDir($dir);
        $path = $dir.'/'.$key.'.'.$ext;
        if (!is_file($path)) {
            $img = $p->apply($this->driver);
            $blob = $this->driver->encode($img, new Format($mime, $ext), $options);
            file_put_contents($path, $blob);
        }
        return $path;
    }
    public function cacheUrl(\MaximKlassen\Image\Pipeline\Pipeline $p, string $targetFormat, array $options = []): ?string {
        $path = $this->cache($p, $targetFormat, $options);
        if(!$this->publicRoot || !$this->publicPrefix) return null;
        $realRoot = realpath($this->publicRoot); $real = realpath($path);
        if(!$realRoot || !$real) return null;
        if(str_starts_with($real, $realRoot)){ $rel = ltrim(substr($real, strlen($realRoot)),'/'); return rtrim($this->publicPrefix,'/').'/'.$rel; }
        return null;
    }
}
