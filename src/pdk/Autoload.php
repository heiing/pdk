<?php

namespace heiing\pdk;

/**
 * AutoLoad
 *
 * @author hzm
 */
class Autoload {
    
    private static $registed = false;
    
    /**
     * 注册 heiing\pdk 的类和接口的自动加载函数
     * @return boolean 成功返回 true，失败返回 false;
     */
    public static function regist() {
        if (true === self::$registed) {
            return true;
        }
        self::$registed = spl_autoload_register(function ($class_name) {
            if (empty($class_name)) {
                return false;
            }
            if ("\\" === $class_name{0}) {
                $class_name = ltrim($class_name, "\\");
            }
            $nslen = strlen(__NAMESPACE__);
            if (__NAMESPACE__ !== substr($class_name, 0, $nslen)) {
                return false;
            }
            $file = __DIR__ . strtr(substr($class_name, $nslen), "\\", DIRECTORY_SEPARATOR) . ".php";
            if (!is_file($file)) {
                return false;
            }
            require $file;
            if (!class_exists($class_name)) {
                return false;
            }
            return true;
        });
        return self::$registed;
    }
    
}
