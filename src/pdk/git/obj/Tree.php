<?php

namespace heiing\pdk\git\obj;

/**
 * Tree
 *
 * @author hzm
 */
class Tree extends Obj {
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'tree';
    }

    public function parseFromLines($lines) {
        
    }

}
