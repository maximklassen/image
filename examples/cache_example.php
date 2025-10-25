<?php
require __DIR__.'/../vendor/autoload.php';
use MaximKlassen\Image\ImageManager;
use MaximKlassen\Image\Pipeline\Pipeline;
$manager = ImageManager::withDefaults();
$manager->setCacheRoot(__DIR__.'/cache');
$manager->setPublicMapping(__DIR__, '/static');
$img = $manager->readFromPath(__DIR__.'/input.jpg');
$p = Pipeline::from($img)->autoOrient()->fit(800,600,'cover')->stripMetadata();
$path = $manager->cache($p, 'webp', ['quality'=>80]);
$url  = $manager->cacheUrl($p, 'webp', ['quality'=>80]);
echo "Cached at: {$path}\n";
echo "URL: ".($url ?? '(no mapping)')."\n";
