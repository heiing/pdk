<?php

namespace heiing\pdk\logs\formatter;

/**
 * Formatter: 对日志消息进行格式化
 * @author hzm
 */
interface Formatter {
    
    /**
     * 格式化日志消息
     * @param string $message
     * @return string 格式化之后的消息
     */
    public function format($level, $message);
    
}
