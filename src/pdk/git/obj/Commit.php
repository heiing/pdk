<?php

namespace heiing\pdk\git\obj;

/**
 * Commit
 *
 * @author hzm
 */
class Commit extends Obj {
    
    private $treeId;
    private $parents = [];
    
    private $authorName = null;
    private $authorMail = null;
    private $authorTime = 0;
    private $authorTimezone = null;
    
    private $committerName = null;
    private $committerMail = null;
    private $committerTime = 0;
    private $committerTimezone = null;

    private $message = null;

    public function __construct($id) {
        parent::setId($id);
    }
    
    /**
     * 返回 commit
     * @return string
     */
    public function getType() {
        return 'commit';
    }
    
    /**
     * 返回 tree 对象的 sha-1
     * @return string
     */
    public function getTreeId() {
        return $this->treeId;
    }
    
    /**
     * 返回 commit 的上游节点 sha-1 的集合
     * @return array
     */
    public function getParents() {
        return $this->parents;
    }
    
    /**
     * 返回 author 的姓名
     * @return string
     */
    public function getAuthorName() {
        return $this->authorName;
    }
    
    /**
     * 返回 author 的 email 地址
     * @return string
     */
    public function getAuthorEmail() {
        return $this->authorMail;
    }
    
    /**
     * 返回 author timestamp (秒)，例如 1467771902
     * @return int
     */
    public function getAuthorTime() {
        return $this->authorTime;
    }
    
    /**
     * 返回 author timezone，例如 +0800
     * @return string
     */
    public function getAuthorTimezone() {
        return $this->authorTimezone;
    }
    
    /**
     * 返回 committer 的姓名
     * @return string
     */
    public function getCommitterName() {
        return $this->committerName;
    }
    
    /**
     * 返回 committer 的 email 地址
     * @return string
     */
    public function getCommitterEmail() {
        return $this->committerMail;
    }
    
    /**
     * 返回 committer timestamp (秒)，例如 1467771902
     * @return int
     */
    public function getCommitterTime() {
        return $this->committerTime;
    }
    
    /**
     * 返回 committer timezone，例如 +0800
     * @return string
     */
    public function getCommitterTimezone() {
        return $this->committerTimezone;
    }

    public function parseFromLines($lines) {
        $msgStarted = false;
        $msg = '';
        foreach ($lines as $line) {
            $ln = trim($line);
            if ((false === $msgStarted) && empty($ln)) {
                $msgStarted = true;
                continue;
            }
            if ($msgStarted) {
                $msg .= $line;
                continue;
            }
            list($name, $value) = explode(' ', $ln, 2);
            switch ($name) {
                case 'tree':
                    $this->treeId = $value;
                    break;
                case 'parent':
                    $this->parents[] = $value;
                    break;
                case 'author':
                    $this->parseAuthor($value);
                    break;
                case 'committer':
                    $this->parseCommitter($value);
                    break;
            }
        }
        $this->message = trim($msg);
    }
    
    private function parseAuthor($info) {
        $man = $this->parseMan($info);
        $this->authorMail = $man[2];
        $this->authorName = $man[1];
        $this->authorTime = $man[3];
        $this->authorTimezone = $man[4];
    }
    
    private function parseCommitter($info) {
        $man = $this->parseMan($info);
        $this->committerMail = $man[2];
        $this->committerName = $man[1];
        $this->committerTime = $man[3];
        $this->committerTimezone = $man[4];
    }
    
    private function parseMan($info) {
        $matches = [];
        preg_match('/^\\s*(.+?)\\s\\<([a-zA-Z0-9._-]+@[^>]+)\\>\s+(\\d+)\\s+([+-]?\\d+)\\s*$/', $info, $matches);
        return $matches;
    }
}
