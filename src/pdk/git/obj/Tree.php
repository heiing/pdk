<?php

namespace heiing\pdk\git\obj;

/**
 * Tree
 *
 * @author hzm
 */
class Tree extends Obj {
    
    private $blobs = [];
    private $trees = [];
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'tree';
    }

    public function parseFromLines($lines) {
        foreach ($lines as $line) {
            $ln = trim($line);
            list($perm, $type, $id, $name) = explode(" ", $ln, 4);
            $item = ['id' => trim($id), 'perm' => trim($perm), 'name' => trim($name)];
            switch (trim($type)) {
                case 'tree':
                    $this->trees[] = $item;
                    break;
                case 'blob':
                    $this->blobs[] = $item;
                    break;
            }
        }
    }

}
