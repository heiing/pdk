<?php

namespace heiing\pdk\git\obj;

/**
 * Tag
 *
 * @author hzm
 */
class Tag extends Obj {
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'tag';
    }

    public function parseFromLines($lines) {
        
    }
    
}
