<?php

namespace heiing\pdk\logs\formatter;

use heiing\pdk\logs\Level;

/**
 * BasicFormatter 基本的格式器
 *
 * @author hzm
 */
class BasicFormatter implements Formatter {
    
    private $ln = PHP_EOL;
    private $timeFormat = 'Y-m-d H:i:s';
    
    public function __construct() {
    }
    
    public function setTimeForamt($format) {
        $this->timeFormat = $format;
        return $this;
    }
    
    public function format($level, $message) {
        return date($this->timeFormat) . ' [' . Level::getName($level) . "] {$message}{$this->ln}";
    }
    
}
