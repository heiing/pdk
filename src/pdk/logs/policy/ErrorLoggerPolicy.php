<?php

namespace heiing\pdk\logs\policy;

use heiing\pdk\logs\Level;

/**
 * PHP 错误日志策略
 *
 * @author hzm
 */
class ErrorLoggerPolicy extends Policy {
    
    private $errorLevelMap = [];
    
    /**
     * 设置某个 logger 水平的错误代码。
     * 
     * @param int $level        例如 Level::WARN
     * @param array $errorCodes 例如 [E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING]
     * @return \heiing\pdk\logs\policy\ErrorLoggerPolicy
     */
    public function setLevelErrorCodes($level, $errorCodes) {
        foreach ($errorCodes as $code) {
            $this->errorLevelMap[$code] = $level;
        }
        return $this;
    }
    
    /**
     * 通过错误代码，查询 logger 水平
     * @param int $errerCode    例如 E_WARNING
     * @param int $defaultLevel 默认 Level::INFO
     * @return int 例如 Level::WARN
     */
    public function getLevelByErrorCode($errerCode, $defaultLevel = Level::INFO) {
        return isset($this->errorLevelMap[$errerCode]) ? $this->errorLevelMap[$errerCode] : $defaultLevel;
    }
    
}
