<?php

namespace heiing\pdk\file\tree;

/**
 * RecursiveDirectory
 *
 * @author hzm
 */
class RecursiveDirectory implements \Iterator {
    
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
    
    /**
     * 
     * @param string $rootDirectoryPath
     * @param int $mode @see http://php.net/manual/en/recursiveiteratoriterator.construct.php
     */
    public function __construct($rootDirectoryPath, $mode = \RecursiveIteratorIterator::SELF_FIRST) {
        $this->root = $rootDirectoryPath;
        $this->dir = new \RecursiveDirectoryIterator($this->root, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $this->itr = new \RecursiveIteratorIterator($this->dir, $mode);
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
     * @param \Iterator $itr     目录迭代器，默认为 null，可指定为 matchRegex 的返回值
     */
    public function eachEntry($callback, \Iterator $itr = null) {
        if (null === $itr) {
            $itr = $this->itr;
        }
        foreach ($itr as $name => $splFileInfo) {
            $callback($name, $splFileInfo);
        }
    }

    public function current() {
        return $this->itr->current();
    }

    public function key() {
        return $this->itr->key();
    }

    public function next() {
        $this->itr->next();
    }

    public function rewind() {
        $this->itr->rewind();
    }

    public function valid() {
        return $this->itr->valid();
    }

}
