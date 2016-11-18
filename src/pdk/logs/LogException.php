<?php

namespace heiing\pdk\logs;

/**
 * LogException
 *
 * @author hzm
 */
class LogException extends \RuntimeException {
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function writerNotExists($message) {
        return new LogException("Writer Not Exists: {$message}", 1);
    }
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function formatterNotExists($message) {
        return new LogException("Writer Not Exists: {$message}", 2);
    }
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function levelUndefined($message) {
        return new LogException("Level Undefined: {$message}", 3);
    }
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function levelIllegal($message) {
        return new LogException("Level Illegal: {$message}", 4);
    }
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function badFile($message) {
        return new LogException("Bad File: {$message}", 5);
    }
    
    /**
     * 
     * @param string $message
     * @return \heiing\pdk\logs\LogException
     */
    public static function badLogger($message) {
        return new LogException("Bad Logger: {$message}", 6);
    }
    
}
