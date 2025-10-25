<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Cache;
use MaximKlassen\Image\Contracts\CacheInterface;
final class NullCache implements CacheInterface { public function get(string $key): ?string { return null; } public function set(string $key, string $blob, ?int $ttl = null): void {} }
