<?php

namespace test\pdk;

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src/pdk/Autoload.php';

use heiing\pdk\Autoload;

if (!defined('STDERR')) {
    define('STDERR', fopen("php://stderr", 'wb'));
}

AutoloadTest::main();

/**
 * AutoloadTest
 *
 * @author hzm
 */
class AutoloadTest {
    
    public static function main() {
        $test = new AutoloadTest();
        $test->testRegist();
        $test->testLoaded();
        echo "Test OK\n";
        exit(0);
    }
    
    public static function faild($msg, $line, $file = __FILE__) {
        fwrite(STDERR, "Test Faild: {$msg} @[{$file}]:[{$line}]\n");
        exit(1);
    }
    
    public function testRegist() {
        $registed = Autoload::regist();
        if (true !== $registed) {
            self::faild("Expact: [true], Actual: [false]", __LINE__);
        }
    }
    
    public function testLoaded() {
        $test = new \heiing\pdk\test\Test();
        if (!class_exists("heiing\\pdk\\test\\Test")) {
            self::faild("Expact: [class exists], Actual: [class not found]", __LINE__);
        }
        if (!($test instanceof \heiing\pdk\test\Test)) {
            self::faild("Expact: [class is instanceof heiing\\pdk\\test\\Test], Actual: [no]", __LINE__);
        }
    }
    
}
