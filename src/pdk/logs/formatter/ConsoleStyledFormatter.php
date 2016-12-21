<?php

namespace heiing\pdk\logs\formatter;

use heiing\pdk\logs\console\Style;
use heiing\pdk\logs\Level;

/**
 * ConsoleStyledFormatter
 *
 * @author hzm
 */
class ConsoleStyledFormatter extends BasicFormatter {
    
    private $styles = [];
    
    public function setStyle($levels, Style $style) {
        for ($i = 0; $i <= Level::TopExponent; $i++) {
            $level = 1 << $i;
            if (Level::hasLevel($levels, $level)) {
                $this->styles[$level] = $style;
            }
        }
        return $this;
    }
    
    public function format($level, $message) {
        $fmtString = parent::format($level, $message);
        if (!isset($this->styles[$level])) {
            return $fmtString;
        }
        $styles = $this->styles[$level]->toString();
        if (empty($styles)) {
            return $fmtString;
        }
        return "\033[{$styles}m{$fmtString}\033[0m";
    }
    
}
