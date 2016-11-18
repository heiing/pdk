<?php

namespace heiing\pdk\logs\formatter;

use heiing\pdk\logs\Level;

/**
 * BasicFormatter 基本的格式器
 *
 * @author hzm
 */
class BasicFormatter implements Formatter {
    
    private $ln = "\n";
    private $timeFormat = 'Y-m-d H:i:s';
    
    public function __construct() {
        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            $this->ln = "\r\n";
        }
    }
    
    public function setTimeForamt($format) {
        $this->timeFormat = $format;
        return $this;
    }
    
    public function format($level, $message) {
        return sprintf("%s [%s] %s%s", date($this->timeFormat), Level::getName($level), $message, $this->ln);
    }
    
}
