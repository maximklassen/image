<?php
require __DIR__.'/../vendor/autoload.php';
use MaximKlassen\Image\ImageManager;
use MaximKlassen\Image\Pipeline\Pipeline;
$manager = ImageManager::withDefaults();
$img = $manager->readFromPath(__DIR__.'/input.jpg');
$p = Pipeline::from($img)->autoOrient()->fit(1200,800,'cover',['center','center'])->stripMetadata()->convert('webp');
$result = $p->apply($manager->getDriver());
$manager->writeToPath($result, __DIR__.'/output.webp', null, ['quality'=>82]);
echo "Done\n";
