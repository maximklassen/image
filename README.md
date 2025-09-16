
# maximklassen/image

Framework-agnostic PHP image manipulation library with pipeline operations and a GD driver.  
EXIF auto-orientation, crop and fit (cover/contain) are available out of the box.

> Laravel bridge will be provided in a separate package (`maximklassen/image-laravel`).

## Requirements
- PHP ^8.2
- ext-gd (required)
- ext-exif (recommended)

## Quick start
```php
use MaximKlassen\Image\ImageManager;
use MaximKlassen\Image\Pipeline\Pipeline;

$manager = ImageManager::withDefaults(); // chooses GD
$img = $manager->readFromPath(__DIR__.'/input.jpg');

$pipeline = Pipeline::from($img)
    ->autoOrient()
    ->fit(1200, 800, mode: 'cover', position: ['center', 'center'])
    ->stripMetadata()
    ->convert('webp');

$result = $pipeline->apply($manager->getDriver());
// Convert already changed the target format, so pass null:
$manager->writeToPath($result, __DIR__.'/output.webp', null, ['quality' => 82]);
```

## Roadmap
- File cache with deterministic hashes
- Watermark operation
- Convert, StripMetadata
- Imagick driver
- Laravel bridge (Service Provider, Facade, helpers)
