<?php

namespace heiing\pdk\git\obj;

use heiing\pdk\git\GitException;

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
        throw new GitException("UnImplemented, lines: " . implode("\n", $lines));
    }
    
}
