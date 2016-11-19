<?php

namespace heiing\pdk\logs\console;

/**
 * TextAttribute
 *
 * @author hzm
 */
class TextAttributes {
    
    const BOLD    = 1;
    const DIM     = 2;
    const SITM    = 3;
    const SMUL    = 4;
    const BLINK   = 5;
    const REVERSE = 7;
    
    private $attrs = [];
    
    public function __construct($attr) {
        $this->attrs[$attr] = $attr;
    }
    
    public function setAttr($attr) {
        $this->attrs[$attr] = $attr;
        return $this;
    }
    
    public function delAttr($attr) {
        unset($this->attrs[$attr]);
        return $this;
    }
    
    public function getAttrs() {
        return $this->attrs;
    }
    
    public function isEmpty() {
        return count($this->attrs) === 0;
    }
    
    public function toString() {
        return implode(";", $this->attrs);
    }
    
    public function __toString() {
        return $this->toString();
    }
    
}
