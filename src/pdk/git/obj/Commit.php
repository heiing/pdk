<?php

namespace heiing\pdk\git\obj;

/**
 * Commit
 *
 * @author hzm
 */
class Commit extends Obj {
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'commit';
    }

    public function parseFromLines($lines) {
        
    }

}
