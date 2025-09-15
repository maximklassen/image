<?php
require __DIR__.'/../vendor/autoload.php';

use MaximKlassen\Image\ImageManager;
use MaximKlassen\Image\Pipeline\Pipeline;

$in = __DIR__.'/input.jpg';
$out = __DIR__.'/output.webp';

$manager = ImageManager::withDefaults();
$img = $manager->readFromPath($in);

$pipeline = Pipeline::from($img)
    ->autoOrient()
    ->fit(1200, 800, 'cover', ['center','center']);

$result = $pipeline->apply($manager->getDriver());
$manager->writeToPath($result, $out, 'webp', ['quality' => 82]);

echo "Wrote: {$out}\n";
