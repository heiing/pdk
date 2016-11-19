<?php

namespace heiing\pdk\logs\policy;

use heiing\pdk\logs\Level;
use heiing\pdk\logs\LogException;
use heiing\pdk\logs\writer\Writer;
use heiing\pdk\logs\formatter\Formatter;

/**
 * Policy: 日志记录策略
 *
 * @author hzm
 */
abstract class Policy {
    
    private $levels = Level::INFO | Level::WARN | Level::ERROR;
    
    private $writers = [];
    private $formatters = [];
    
    /**
     * 设置日志记录水平
     * @param int $levels 例如 \heiing\pdk\logs\Level::WARN | \heiing\pdk\logs\Level::ERROR
     * @return \heiing\pdk\logs\policy\Policy
     */
    public function setLevels($levels) {
        $this->levels = $levels;
        return $this;
    }
    
    /**
     * 查询已经设置的日志记录水平
     * @return int
     */
    public function getLevels() {
        return $this->levels;
    }
    
    /**
     * 判断是否已经包含了指定的日志水平
     * @param int $level
     * @return boolean
     */
    public function hasLevel($level) {
        return Level::hasLevel($this->getLevels(), $level);
    }
    
    /**
     * 获取指定日志水平的 Writer
     * @param int $level
     * @return \heiing\pdk\logs\writer\Writer
     * @throws \heiing\pdk\logs\LogException
     */
    public function getWriter($level) {
        if (!isset($this->writers[$level])) {
            throw LogException::writerNotExists("Level [$level " . Level::getName($level) . "]");
        }
        return $this->writers[$level];
    }
    
    /**
     * 设置指定水平的 Writer
     * @param int $levels
     * @param \heiing\pdk\logs\writer\Writer $writer
     * @return \heiing\pdk\logs\policy\Policy
     */
    public function setWriter($levels, Writer $writer) {
        for ($i = 0; $i <= Level::TopExponent; $i++) {
            if ($this->hasLevel($i) && Level::hasLevel($levels, $i)) {
                $this->writers[1 << $i] = $writer;
            }
        }
        return $this;
    }
    
    /**
     * 获取指定日志水平的 Formatter
     * @param int $level
     * @return \heiing\pdk\logs\formatter\Formatter
     * @throws \heiing\pdk\logs\LogException
     */
    public function getFormatter($level) {
        if (!isset($this->formatters[$level])) {
            throw LogException::formatterNotExists("Level [$level " . Level::getName($level) . "]");
        }
        return $this->formatters[$level];
    }
    
    /**
     * 获取指定日志水平的 Formatter
     * @param int $levels
     * @param \heiing\pdk\logs\formatter\Formatter $formatter
     * @return \heiing\pdk\logs\policy\Policy
     */
    public function setFormatter($levels, Formatter $formatter) {
        for ($i = 0; $i <= Level::TopExponent; $i++) {
            if ($this->hasLevel($i) && Level::hasLevel($levels, $i)) {
                $this->formatters[1 << $i] = $formatter;
            }
        }
        return $this;
    }
    
}
