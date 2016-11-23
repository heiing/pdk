<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src/pdk/Autoload.php';

use heiing\pdk\Autoload;
use heiing\pdk\test\Tester;

Autoload::regist();

$testPath = __DIR__ . DIRECTORY_SEPARATOR . 'pdk';

$tester = new Tester($testPath, function ($pathname, Tester $tester) {
    return '\\test\\pdk' . strtr(substr($pathname, strlen($tester->getTestPath()), -4), "/", '\\');
});

$tester->setIgnoredCallback(function ($pathname, Tester $tester) {
    if ($pathname === $tester->getTestPath() . DIRECTORY_SEPARATOR . 'AutoloadTest.php') {
        return true;
    }
});

$tester->run();
