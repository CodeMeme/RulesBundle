<?php

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Cant find ' . $file);
}

$autoload = require_once $file;
