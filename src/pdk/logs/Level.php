<?php

namespace heiing\pdk\logs;

/**
 * Level 定义日志的记录水平，日志水平可以理解为日志类型。<br />
 * 
 * 内置的日志水平有：<ol>
 * <li>DEBUG</li>
 * <li>INFO</li>
 * <li>WARN</li>
 * <li>ERROR</li>
 * </ol>
 * 
 * 通过 Level::define 方法，可以自定义更多的日志水平。
 *
 * @author hzm
 */
class Level {
    
    const DEBUG = 1;
    const INFO  = 2;
    const WARN  = 4;
    const ERROR = 8;
    
    const TopExponent = 30;

    private static $extends = [];
    
    /**
     * 定义一个日志水平。
     * @param int $level   必须大于等于 16 且小于等于 2147483648；必须是 2 的幂，指数为 4 到 30。
     * @param string $name 名称
     * @throws \heiing\pdk\logs\LogException
     */
    public static function define($level, $name) {
        $max = 1 << self::TopExponent;
        if ($level < 16 || $level > $max) {
            throw LogException::levelIllegal("level [{$level}] must not less than 16, and must not grater than {$max}");
        }
        if (isset(self::$extends[$level])) {
            throw LogException::levelIllegal("level [{$level}] has been defined with name '" . Level::getName($level) . "'");
        }
        $isok = false;
        for ($i = 4; $i <= self::TopExponent; $i++) {
            if ($level === (1 << $i)) {
                $isok = true;
                break;
            }
        }
        if (false === $isok) {
            throw LogException::levelIllegal("level [{$level}] must be an integer of power of 2, and the exponent is less or equals than " . self::TopExponent);
        }
        self::$extends[$level] = $name;
    }

    /**
     * 查询日志水平的名称
     * @param int $level 日志水平
     * @return string    日志水平名
     * @throws \heiing\pdk\logs\LogException
     */
    public static function getName($level) {
        switch ($level) {
            case self::DEBUG:
                return 'DEBUG';
            case self::INFO:
                return 'INFO';
            case self::WARN:
                return 'WARN';
            case self::ERROR:
                return 'ERROR';
            default :
                if (!isset(self::$extends[$level])) {
                    throw LogException::levelUndefined("Level Number [{$level}]");
                }
                return self::$extends[$level];
        }
    }
    
    /**
     * 通过日志水平名称查询日志水平
     * @param string $name 日志水平名
     * @return int         日志水平
     * @throws \heiing\pdk\logs\LogException
     */
    public static function getLevelByName($name) {
        $ucname = strtoupper(trim($name));
        switch ($ucname) {
            case 'DEBUG':
                return self::DEBUG;
            case 'INFO':
                return self::INFO;
            case 'WARN':
                return self::WARN;
            case 'ERROR':
                return self::ERROR;
            default :
                foreach (self::$extends as $level => $lvname) {
                    if ($ucname === strtoupper(trim($lvname))) {
                        return $level;
                    }
                }
                throw LogException::levelUndefined("Level Name [{$name}]");
        }
    }
    
    /**
     * 检查 $levels 中是否包含 $checkLevel
     * @param int $levels
     * @param int $checkLevel
     * @return boolean
     */
    public static function hasLevel($levels, $checkLevel) {
        return ($levels & $checkLevel) === $checkLevel;
    }
    
}
