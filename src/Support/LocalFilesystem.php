<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Support;
use MaximKlassen\Image\Contracts\FilesystemInterface;
final class LocalFilesystem implements FilesystemInterface {
    public function __construct(private ?string $publicRoot = null, private ?string $publicPrefix = null) {}
    public function read(string $path): string { $data=@file_get_contents($path); if($data===false) throw new \RuntimeException("Cannot read file: {$path}"); return $data; }
    public function write(string $path, string $data): void { $dir=dirname($path); if(!is_dir($dir)){ if(!@mkdir($dir,0775,true)&&!is_dir($dir)) throw new \RuntimeException("Cannot create dir: {$dir}"); } if(@file_put_contents($path,$data)===false) throw new \RuntimeException("Cannot write file: {$path}"); }
    public function exists(string $path): bool { return is_file($path); }
    public function publicUrl(string $path): ?string { if(!$this->publicRoot||!$this->publicPrefix) return null; $realRoot=realpath($this->publicRoot); $real=realpath($path); if(!$realRoot||!$real) return null; if(str_starts_with($real,$realRoot)){ $rel=ltrim(substr($real,strlen($realRoot)),'/'); return rtrim($this->publicPrefix,'/').'/'.$rel; } return null; }
}
