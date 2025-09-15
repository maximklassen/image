<?php
declare(strict_types=1);

namespace MaximKlassen\Image\Contracts;

interface FilesystemInterface
{
    public function read(string $path): string;
    public function write(string $path, string $data): void;
    public function exists(string $path): bool;
    public function publicUrl(string $path): ?string;
}
