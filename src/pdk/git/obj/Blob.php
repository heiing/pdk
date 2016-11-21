<?php

namespace heiing\pdk\git\obj;

/**
 * Blob
 *
 * @author hzm
 */
class Blob extends Obj {
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'blob';
    }

    public function parseFromLines($lines) {
        
    }

}
