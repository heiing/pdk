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
    
    private static $asserts = 0;
    private static $failds = 0;
    private static $passes = 0;
    
    public static function getAsserts() {
        return self::$asserts;
    }
    
    public static function getFailds() {
        return self::$failds;
    }
    
    public static function getPasses() {
        return self::$passes;
    }
    
    public static function report() {
        $total = self::getAsserts();
        $passed = self::getPasses();
        $failds = self::getFailds();
        echo "Test DONE: {$total} asserts, {$failds} faild, {$passed} passed!\n";
    }

    private static function faild($msg) {
        ++self::$failds;
        fwrite(STDERR, "Test Faild: {$msg}\n");
        return false;
    }
    
    private static function ok() {
        ++self::$passes;
        return true;
    }
    
    private static function assert($isPass, $faildMessage) {
        ++self::$asserts;
        if (true === $isPass) {
            self::ok();
        } else {
            self::faild($faildMessage);
        }
    }
    
    public static function assertEquals($expect, $actual, $msg = '') {
        self::assert($expect === $actual, "Expect: [{$expect}], Actual: [{$actual}] {$msg}");
    }
    
    public static function assertNotEquals($expect, $actual, $msg = '') {
        self::assert($expect !== $actual, "Expect: [Not {$expect}], Actual: [{$actual}] {$msg}");
    }
    
    public static function assertTrue($actual, $msg = '') {
        self::assert(true === $actual, "Expect: [true], Actual: [false] {$msg}");
    }
    
    public static function assertFalse($actual, $msg = '') {
        self::assert(false === $actual, "Expect: [false], Actual: [true] {$msg}");
    }
    
    public static function assertNull($actual, $msg = '') {
        self::assert(null === $actual, "Expect: [null], Actual: [{$actual}] {$msg}");
    }
    
    public static function assertNotNull($actual, $msg = '') {
        self::assert(null !== $actual, "Expect: [Not null], Actual: [null] {$msg}");
    }
    
}
