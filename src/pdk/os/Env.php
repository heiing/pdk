<?php

namespace heiing\pdk\os;

/**
 * Env
 *
 * @author hzm
 */
class Env {
    
    /**
     * 获取操作系统类型，如果是 Windows 类，则返回 Windows，其它情况返回 PHP_OS，
     * 可能的值包含但不限于：Linux, Darwin, Unix
     * @return string
     */
    public static function getOperationSystemType() {
        if (('\\' === DIRECTORY_SEPARATOR) && ("\r\n" === PHP_EOL) && ('WIN' === strtoupper(substr(PHP_OS, 0, 3)))) {
            return 'Windows';
        }
        return PHP_OS;
    }
    
    /**
     * 判断当前操作系统是否为 Microsoft Windows
     * @return boolean
     */
    public static function isWindows() {
        return 'Windows' === self::getOperationSystemType();
    }
    
    /**
     * 判断当前操作系统是否为 Apple Os
     * @return boolean
     */
    public static function isAppleOs() {
        return 'Darwin' === self::getOperationSystemType();
    }
    
    /**
     * 判断当前操作系统是否为 Linux
     * @return boolean
     */
    public static function isLinux() {
        return 'Linux' === self::getOperationSystemType();
    }
    
    /**
     * 获取环境变量
     * @param string $name
     * @return string 如果环境变量不存在，则返回 null
     */
    public static function get($name) {
        $ret = getenv($name);
        if (false === $ret) {
            return null;
        }
        return $ret;
    }
    
    /**
     * 设置环境变量
     * @param string $name  环境变量名
     * @param string $value 环境变量值
     * @return boolean 成功返回 true，失败返回 false
     */
    public static function set($name, $value) {
        return putenv("{$name}={$value}");
    }
    
}
