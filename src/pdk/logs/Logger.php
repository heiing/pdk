<?php

namespace heiing\pdk\logs;

use heiing\pdk\logs\formatter\BasicFormatter;
use heiing\pdk\logs\policy\Policy;
use heiing\pdk\logs\policy\ErrorLoggerPolicy;
use heiing\pdk\logs\writer\FileWriter;

/**
 * Logger: 日志记录器
 *
 * @author hzm
 */
class Logger {
    
    private $policy;
    
    /**
     * 使用指定的策略构建日志记录器
     * @param \heiing\pdk\logs\policy\Policy $policy
     */
    public function __construct(Policy $policy) {
        $this->policy = $policy;
    }
    
    /**
     * 设置日志策略
     * @param \heiing\pdk\logs\policy\Policy $policy
     * @return \heiing\pdk\logs\Logger
     */
    public function setPolicy(Policy $policy) {
        $this->policy = $policy;
        return $this;
    }
    
    /**
     * 获取 Logger 中的策略
     * @return \heiing\pdk\logs\policy\Policy
     */
    public function getPolicy() {
        return $this->policy;
    }
    
    /**
     * 按指定的日志水平写入日志
     * @param int $level      日志水平
     * @param string $message 日志消息
     * @return int 返回已经写入的日志的字节数，如果日志水平没在策略中，则返回 0
     */
    public function writeLevelMessage($level, $message) {
        if (!$this->policy->hasLevel($level)) {
            return 0;
        }
        $writer = $this->policy->getWriter($level);
        return $writer->write($this->policy->getFormatter($level)->format($level, $message));
    }
    
    /**
     * 按水平名写入日志
     * @param string $levelName
     * @param string $message
     * @return int
     */
    public function writeLevel($levelName, $message) {
        return $this->writeLevelMessage(Level::getLevelByName($levelName), $message);
    }
    
    /**
     * 写入 Debug 水平的日志
     * @param string $message
     * @return int
     */
    public function writeDebug($message) {
        return $this->writeLevelMessage(Level::DEBUG, $message);
    }
    
    /**
     * 写入 Info 水平的日志
     * @param string $message
     * @return int
     */
    public function writeInfo($message) {
        return $this->writeLevelMessage(Level::INFO, $message);
    }
    
    /**
     * 写入 Warn 水平的日志
     * @param string $message
     * @return int
     */
    public function writeWarn($message) {
        return $this->writeLevelMessage(Level::WARN, $message);
    }
    
    /**
     * 写入 Error 水平的日志
     * @param string $message
     * @return int
     */
    public function writeError($message) {
        return $this->writeLevelMessage(Level::ERROR, $message);
    }
    
    /**
     * 默认的日志记录器
     * @var \heiing\pdk\logs\Logger
     */
    private static $logger = null;
    
    /**
     * 设置 Logger
     * @param \heiing\pdk\logs\Logger $logger
     * @throws \heiing\pdk\logs\LogException
     */
    public static function setLogger(Logger $logger) {
        if (null === $logger) {
            throw LogException::badLogger("Logger must not be NULL");
        }
        self::$logger = $logger;
    }
    
    /**
     * 使用默认或通过 \heiing\pdk\logs\Logger::setLogger 设置的记录器写入日志
     * @param int $level
     * @param string $message
     * @return int
     */
    public static function write($level, $message) {
        if (null === self::$logger) {
            self::$logger = self::newDefaultLogger();
        }
        return self::$logger->writeLevelMessage($level, $message);
    }
    
    /**
     * 使用默认或通过 \heiing\pdk\logs\Logger::setLogger 设置的记录器写入 Debug 水平的日志
     * @param string $message
     * @return int
     */
    public static function debug($message) {
        return self::write(Level::DEBUG, $message);
    }
    
    /**
     * 使用默认或通过 \heiing\pdk\logs\Logger::setLogger 设置的记录器写入 Info 水平的日志
     * @param string $message
     * @return int
     */
    public static function info($message) {
        return self::write(Level::INFO, $message);
    }
    
    /**
     * 使用默认或通过 \heiing\pdk\logs\Logger::setLogger 设置的记录器写入 Warn 水平的日志
     * @param string $message
     * @return int
     */
    public static function warn($message) {
        return self::write(Level::WARN, $message);
    }
    
    /**
     * 使用默认或通过 \heiing\pdk\logs\Logger::setLogger 设置的记录器写入 Error 水平的日志
     * @param string $message
     * @return int
     */
    public static function error($message) {
        return self::write(Level::ERROR, $message);
    }
    
    /**
     * 创建默认的日志记录器
     * @return \heiing\pdk\logs\Logger 
     */
    public static function newDefaultLogger() {
        $policy = new ErrorLoggerPolicy();
        $stdErr = new FileWriter(defined('STDERR') ? STDERR : "php://stderr");
        $fmtter = new BasicFormatter();
        $policy->setWriter($policy->getLevels(), $stdErr);
        $policy->setFormatter($policy->getLevels(), $fmtter);
        return new Logger($policy);
    }
}
