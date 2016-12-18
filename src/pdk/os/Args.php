<?php

namespace heiing\pdk\os;

/**
 * Args 解析命令行参数：<br />
 * 参数类型：
 * <ul>
 * <li>flag: 使用一个 - 符号引导的参数，例如 -abc 为三个 flag: a, b, c</li>
 * <li>param: 使用两个 - 符号引导的参数，例如 --name John 的参数名为 name ，值为 John</li>
 * <li>remainder：没有 - 符号引导的参数，例如 a b c 则有三个 remainder：a, b, c，对应索引为 0, 1, 2</li>
 * </ul>
 *
 * @author hzm
 */
class Args {
    
    private $argv = [];
    
    private $flags = [];
    private $params = [];
    private $remainders = [];
    
    public function __construct($argv) {
        $this->argv = $argv;
        $this->parse();
    }
    
    /**
     * 查询是否出现 flag。<br />
     * 例如：<br />
     * cmd -abc 结果：<br />
     * - hasFlag('a') // true<br />
     * - hasFlag('c') // true<br />
     * - hasFlag('d') // false<br />
     * @param string $name
     * @return boolean 如果出现 flag 则返回 true, 否则返回 false
     */
    public function hasFlag($name) {
        return isset($this->flags[$name]);
    }
    
    public function getFlags() {
        return $this->flags;
    }
    
    /**
     * 查询参数的值
     * 例如：<br />
     * cmd --name John --age 18 结果：<br />
     * - getParam('name') // John<br />
     * - getParam('age')  // 18<br />
     * - getParam('sex')  // null<br />
     * @param string $name
     * @param string $defaultValue
     * @return string
     */
    public function getParam($name, $defaultValue = null) {
        return isset($this->params[$name]) ? $this->params[$name] : $defaultValue;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    /**
     * 查询剩余参数
     * @param int $index
     * @param string $defaultValue
     * @return string
     */
    public function getRemainder($index, $defaultValue = null) {
        return isset($this->remainders[$index]) ? $this->remainders[$index] : $defaultValue;
    }
    
    public function getRemainders() {
        return $this->remainders;
    }
    
    private function parse() {
        $name = '';
        foreach ($this->argv as $arg) {
            if (('--' === $arg) || ('-' === $arg)) {
                continue;
            }
            if ('-' !== $arg{0}) {
                if (empty($name)) {
                    $this->remainders[] = $arg;
                } else {
                    $this->params[$name] = $arg;
                    $name = '';
                }
                continue;
            }
            if (('-' === $arg{1})) {
                if (!empty($name)) {
                    $this->params[$name] = '';
                }
                $name = substr($arg, 2);
                continue;
            }
            for ($i = 1; $i < strlen($arg); $i++) {
                $this->flags[$arg{$i}] = $arg{$i};
            }
        }
    }
    
}
