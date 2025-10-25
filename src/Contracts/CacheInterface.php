<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Contracts;
interface CacheInterface {
    public function get(string $key): ?string;
    public function set(string $key, string $blob, ?int $ttl = null): void;
}
