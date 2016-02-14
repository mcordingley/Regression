<?php

$archive = new Phar('regression.phar');

$base = dirname(__FILE__);

$archive->buildFromIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base . '/src')), $base);
$archive->buildFromIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base . '/vendor')), $base);

$archive->setStub(Phar::createDefaultStub('vendor/autoload.php'));
