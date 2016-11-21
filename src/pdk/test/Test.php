<?php

namespace heiing\pdk\test;

if (!defined('STDERR')) {
    define('STDERR', fopen("php://stderr", 'wb'));
}

/**
 * Test
 *
 * @author hzm
 */
class Test {
    
    public static function faild($msg) {
        fwrite(STDERR, "Test Faild: {$msg}\n");
        exit(1);
    }
    
    public static function assertEquals($expect, $actual, $msg = '') {
        if ($expect !== $actual) {
            self::faild("Expect: [{$expect}], Actual: [{$actual}] {$msg}");
        }
    }
    
    public static function assertTrue($actual, $msg = '') {
        if (true !== $actual) {
            self::faild("Expect: [true], Actual: [false] {$msg}");
        }
    }
    
    public static function assertFalse($actual, $msg = '') {
        if (true !== $actual) {
            self::faild("Expect: [false], Actual: [true] {$msg}");
        }
    }
    
    public static function assertNull($actual, $msg = '') {
        if (null !== $actual) {
            self::faild("Expect: [null], Actual: [{$actual}] {$msg}");
        }
    }
    
}
