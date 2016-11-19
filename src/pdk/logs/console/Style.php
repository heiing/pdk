<?php

namespace heiing\pdk\logs\console;

/**
 * Style
 *
 * @author hzm
 */
class Style {
    
    private $textAttributes = null;
    private $textFgColor = null;
    private $textBgColor = null;


    public function __construct(Color $bgColor = null, Color $fgColor = null, TextAttributes $attrs = null) {
        $this->textAttributes = $attrs;
        $this->textBgColor = $bgColor;
        $this->textFgColor = $fgColor;
    }
    
    public function setTextAttributes(TextAttributes $attrs) {
        $this->textAttributes = $attrs;
        return $this;
    }
    
    public function setBackgroundColor(Color $color) {
        $this->textBgColor = $color;
        return $this;
    }
    
    
    public function setForegroundColor(Color $color) {
        $this->textFgColor = $color;
        return $this;
    }
    
    public function toString() {
        $ret = '';
        if ((null !== $this->textAttributes) && (!$this->textAttributes->isEmpty())) {
            $ret = $this->textAttributes->toString();
        }
        if (null !== $this->textBgColor) {
            $ret .= (empty($ret) ? ';' : '') . $this->textBgColor->toString();
        }
        if (null !== $this->textFgColor) {
            $ret .= (empty($ret) ? ';' : '') . $this->textFgColor->toString();
        }
        return $ret;
    }
    
    public function __toString() {
        return $this->toString();
    }
    
}
