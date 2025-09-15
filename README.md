
# maximklassen/image

Framework-agnostic PHP image manipulation library with pipeline operations and a GD driver.  
EXIF auto-orientation, crop and fit (cover/contain) are available out of the box.

> Laravel bridge will be provided in a separate package (`maximklassen/image-laravel`).

## Requirements
- PHP ^8.2
- ext-gd (required)
- ext-exif (recommended)

## Install (VCS path)
```json
{
  "require": {
    "maximklassen/image": "*"
  },
  "repositories": [
    { "type": "vcs", "url": "git@github.com:maximklassen/image.git" }
  ]
}
```

## Quick start
```php
use MaximKlassen\Image\ImageManager;
use MaximKlassen\Image\Pipeline\Pipeline;

$manager = ImageManager::withDefaults(); // chooses GD
$img = $manager->readFromPath(__DIR__.'/input.jpg');

$pipeline = Pipeline::from($img)
    ->autoOrient()
    ->fit(1200, 800, mode: 'cover', position: ['center', 'center']);

$result = $pipeline->apply($manager); // returns Image
$manager->writeToPath($result, __DIR__.'/output.webp', format: 'webp', options: ['quality' => 82]);
```

## Roadmap
- File cache with deterministic hashes
- Watermark operation
- Convert, StripMetadata
- Imagick driver
- Laravel bridge (Service Provider, Facade, helpers)
```

