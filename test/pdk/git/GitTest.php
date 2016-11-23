<?php

namespace test\pdk\git;

use heiing\pdk\test\Test;
use heiing\pdk\git\Git;

/**
 * GitTest
 *
 * @author hzm
 */
class GitTest extends Test {
    
    private $git;
    
    public function __construct() {
        $dir = "/tmp/TEST.GIT." . microtime(true);
        mkdir($dir, 0755, true);
        $this->git = new Git($dir);
    }
    
    public function __destruct() {
        exec("rm -rf {$this->git->getPathname()}");
    }
    
    public function testInit() {
        $this->git->init(true);
        self::assertTrue(is_file("{$this->git->getPathname()}/config"));
    }
    
    //public function test
    
}
