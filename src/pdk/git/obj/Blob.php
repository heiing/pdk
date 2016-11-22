<?php

namespace heiing\pdk\git\obj;

/**
 * Blob
 *
 * @author hzm
 */
class Blob extends Obj {
    
    private $content = '';
    
    public function __construct($id) {
        parent::setId($id);
    }
    
    public function getType() {
        return 'blob';
    }
    
    public function getContent() {
        return $this->content;
    }

    public function parseFromLines($lines) {
        $this->content = implode('', $lines);
    }

}
