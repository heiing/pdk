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
        self::$registed = self::registAutoloadFunction(__DIR__, __NAMESPACE__, '.php', __NAMESPACE__);
        return self::$registed;
    }
    
    /**
     * 注册
     * @param string $rootDirectory
     * @param string $namespace
     * @param string $ext
     * @param string $ns_trim_prefix
     * @return boolean
     */
    public static function registAutoloadFunction($rootDirectory, $namespace = '\\', $ext = '.php', $ns_trim_prefix = '') {
        $ok = spl_autoload_register(function ($class_name) use ($rootDirectory, $namespace, $ext, $ns_trim_prefix) {
            if (empty($class_name)) {
                return false;
            }
            if ("\\" === $class_name{0}) {
                $class_name = ltrim($class_name, "\\");
            }
            if ("\\" === $namespace{0}) {
                $namespace = ltrim($namespace, "\\");
            }
            if ("\\" === $ns_trim_prefix) {
                $ns_trim_prefix = ltrim($ns_trim_prefix, "\\");
            }
            
            // 如果属于根命名空间，则直接包含进来
            $cn2p = trim(strtr($class_name, "\\", DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
            $path = dirname($cn2p);
            if ('.' === $path) {
                $file = rtrim($rootDirectory, "/\\") . DIRECTORY_SEPARATOR . basename($cn2p) . $ext;
                if (!is_file($file)) {
                    return false;
                }
                require $file;
                return class_exists($class_name);
            }
            
            // 检查类名是否属于 $namespace 的命名空间下，如果不是，则返回
            $nslen = strlen($namespace);
            if (($nslen > 0) && ($namespace !== substr($class_name, 0, $nslen))) {
                return false;
            }
            
            // 删除命名空间前缀
            $trim = strtr($ns_trim_prefix, "\\", DIRECTORY_SEPARATOR);
            $trlen = strlen($ns_trim_prefix);
            if (!empty($trim) && (strlen($path) > $trlen) && ($trim === substr($path, 0, $trlen))) {
                $path = substr($path, $trlen);
            }
            
            if (strlen($path) > 0) {
                $path = trim($path, DIRECTORY_SEPARATOR);
            }
            
            $file = implode(DIRECTORY_SEPARATOR, [rtrim($rootDirectory, "/\\"), $path, basename($cn2p)]) . $ext;
            if (!is_file($file)) {
                return false;
            }
            
            require $file;
            return class_exists($class_name);
        });
        return $ok;
    }
    
}
