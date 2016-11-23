<?php

namespace heiing\pdk\file\tree;

/**
 * RecursiveDirectory
 *
 * @author hzm
 */
class RecursiveDirectory {
    
    private $root = '';
    
    /**
     *
     * @var \RecursiveDirectoryIterator 
     */
    private $dir;
    /**
     *
     * @var \RecursiveIteratorIterator 
     */
    private $itr;
    
    public function __construct($rootDirectoryPath) {
        $this->root = $rootDirectoryPath;
        $this->dir = new \RecursiveDirectoryIterator($this->root, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $this->itr = new \RecursiveIteratorIterator($this->dir);
    }
    
    /**
     * 
     * @param string $pattern
     * @param int $mode
     * @param int $flags
     * @param int $preg_flags
     * @return \RegexIterator
     */
    public function matchRegex($pattern, $mode = \RegexIterator::MATCH, $flags = 0, $preg_flags = 0) {
        return new \RegexIterator($this->itr, $pattern, $mode, $flags, $preg_flags);
    }
    
    /**
     * 对目录做遍历，callback 接受 $name, $splFileInfo 两个参数
     * @param callable $callback
     */
    public function eachEntry($callback) {
        foreach ($this->itr as $name => $splFileInfo) {
            $callback($name, $splFileInfo);
        }
    }
    
}
