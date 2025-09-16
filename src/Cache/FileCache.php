<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Cache;

use MaximKlassen\Image\Contracts\CacheInterface;

final class FileCache implements CacheInterface
{
    public function __construct(private string $root)
    {
        if (!is_dir($root)) {
            if (!@mkdir($root, 0775, true) && !is_dir($root)) {
                throw new \RuntimeException("Cannot create cache dir: {$root}");
            }
        }
    }

    private function pathFor(string $key): string
    {
        $prefix = substr($key, 0, 2);
        $dir = rtrim($this->root, '/').'/'.$prefix;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir.'/'.$key.'.blob';
    }

    public function get(string $key): ?string
    {
        $path = $this->pathFor($key);
        if (is_file($path)) {
            return file_get_contents($path) ?: null;
        }
        return null;
    }

    public function set(string $key, string $blob, ?int $ttl = null): void
    {
        $path = $this->pathFor($key);
        file_put_contents($path, $blob);
    }
}
