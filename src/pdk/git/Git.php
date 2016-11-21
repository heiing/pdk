<?php

namespace heiing\pdk\git;

use heiing\pdk\git\obj\Blob;
use heiing\pdk\git\obj\Tree;
use heiing\pdk\git\obj\Commit;
use heiing\pdk\git\obj\Tag;

/**
 * Git
 *
 * @author hzm
 */
class Git {
    
    private $git = '.';
    private $bin = 'git';
    
    /**
     * 创建一个 git 对象，用于执行 git 命令
     * @param string $repository git 仓库的路径
     * @param string $bin git 命令的路径
     */
    public function __construct($repository, $bin = 'git') {
        $this->git = $repository;
        $this->bin = $bin;
    }
    
    /**
     * 执行 git 命令
     * @param string $cmd git 命令，如 push, pull 等
     * @return array
     * @throws \heiing\pdk\git\GitException
     */
    private function exec($cmd) {
        $ret = 0;
        $out = [];
        $command = "{$this->bin} {$cmd}";
        exec($command, $out, $ret);
        if (0 !== (int)$ret) {
            throw new GitException("Execute git[exit code: {$ret}] faild: [{$command}], error outputs: " . implode("\n", $out));
        }
        return $out;
    }
    
    /**
     * 通过对象名查询对象的 sha-1 的值
     * @param string $objectName 对象名，如引用名 master, HEAD、tab、sha-1
     * @throws \heiing\pdk\git\GitException
     */
    public function revParse($objectName) {
        if (preg_match('/^[a-z0-9]{32}$/', $objectName) > 0) {
            return $objectName;
        }
        $ret = $this->exec("rev-parse '{$objectName}'");
        return trim($ret[1]);
    }
    
    /**
     * 通过对象名查询对象类型
     * @param string $objectName 对象名，如引用名 master, HEAD、tab、sha-1
     * @return string 对象类型：blob, tree, commit, tag
     */
    public function typeParse($objectName) {
        $rev = $this->revParse($objectName);
        $ret = $this->exec("cat-file -t {$rev}");
        return strtolower(trim($ret[0]));
    }
    
    /**
     * 通过对象名，查找并创建对象
     * @param string $objectName 对象名，如引用名 master, HEAD、tab、sha-1
     * @return \heiing\pdk\git\obj\Obj 
     * @throws \heiing\pdk\git\GitException
     */
    public function objectParse($objectName) {
        $rev = $this->revParse($objectName);
        $type = $this->typeParse($rev);
        $obj = null;
        switch ($type) {
            case 'blob':
                $obj = new Blob($rev);
                break;
            case 'tree':
                $obj = new Tree($rev);
                break;
            case 'commit':
                $obj = new Commit($rev);
                break;
            case 'tag':
                $obj = new Tag($rev);
                break;
            default :
                throw new GitException("Unknow Object Type: {$type}");
        }
        $lines = $this->exec("cat-file -p {$rev}");
        $obj->parseFromLines($lines);
        return $obj;
    }
}
