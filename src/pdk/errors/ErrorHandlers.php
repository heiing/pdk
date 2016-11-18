<?php

namespace heiing\pdk\errors;

use heiing\pdk\logs\Level;
use heiing\pdk\logs\Logger;
use heiing\pdk\logs\policy\ErrorLoggerPolicy;

/**
 * Handles PHP errors
 *
 * @author hzm
 */
class ErrorHandlers {
    
    private static $hasChangedToException = false;
    
    /**
     * 发生所有的错误，都抛出 \ErrorException。<br />
     * 用户定义的错误、非致命的错误：\ErrorException::getCode() 的值为 0；<br />
     * 致命错误：\ErrorException::getCode() 的值为 1
     */
    public static function toException() {
        if (self::$hasChangedToException === true) {
            return;
        }
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
        register_shutdown_function(function () {
            $error = error_get_last();
            if (null === $error) {
                return;
            }
            $errno = (int)$error['type'];
            switch ($errno) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_CORE_WARNING:
                case E_COMPILE_ERROR:
                case E_COMPILE_WARNING:
                    throw new \ErrorException($error['message'], 1, $errno, $error['file'], $error['line']);
            }
        });
        self::$hasChangedToException = true;
    }
    
    public static function toLogger(Logger $logger = null) {
        self::toException();
        if (null === $logger) {
            $logger = Logger::newDefaultLogger();
        }
        set_exception_handler(function (\Exception $e) use ($logger) {
            self::LogException($logger, $e);
        });
    }
    
    public static function LogException(Logger $logger, \Exception $e) {
        $fmt = new BasicExceptionFormatter($e, 20);
        $code = $fmt->getCode();
        $level = Level::ERROR;
        if (($code > 0) && ($logger->getPolicy() instanceof ErrorLoggerPolicy)) {
            $level = $logger->getPolicy()->getLevelByErrorCode($code);
        }
        $logger->writeLevelMessage($level, $fmt->toString());
    }
    
}
