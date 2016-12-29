<?php

namespace heiing\pdk\file;

use heiing\pdk\os\Env;

/**
 * Path
 *
 * @author hzm
 */
class Path extends \SplFileInfo {
    
    /**
     * 创建目录
     * @param string $path        目录路径
     * @param int $perm           目录权限，默认值 0755
     * @param boolean $recursive  父目录不存在时是否创建
     * @return boolean 创建成功返回 true，失败返回 false
     */
    public static function createDirectory($path, $perm = 0755, $recursive = true) {
        return mkdir($path, $perm, $recursive);
    }
    
    /**
     * 删除目录
     * @param string $path       目录路径
     * @param boolean $recursive 是否删除目录本身及其所有子目录与文件
     * @return boolean 删除成功返回 true，失败返回 false
     */
    public static function removeDirectory($path, $recursive = false) {
        if (true === $recursive) {
            $ret = 0;
            $out = [];
            if (Env::isWindows()) {
                exec("RMDIR /S /Q \"{$path}\"", $out, $ret);
            } else {
                exec("rm -rf \"{$path}\"", $out, $ret);
            }
            return 0 === (int)$ret;
        }
        return rmdir($path);
    }

    /**
     * 路径连接。例如 join('a', 'b', 'c') 输出（Linux）'a/b/c' 或（Windows）'a\b\c'
     * @return string
     */
    public static function join() {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }
    
    /**
     * 从 PATH 环境变更的目录中查找可执行文件 $name
     * @param string $name
     * @return string      如果找到则返回该执行文件的绝对路径，否则返回 null
     * @throws FileException
     */
    public static function which($name) {
        if ((false !== strpos($name, '/')) || (false !== strpos($name, '\\'))) {
            throw new FileException("Invalid name: {$name}");
        }
        $path = Env::get('PATH');
        if (null === $path) {
            return null;
        }
        $paths = explode(PATH_SEPARATOR, $path);
        $is_windows = Env::isWindows();
        foreach ($paths as $p) {
            $files[] = self::join($p, $name);
            if ($is_windows) {
                foreach (['.exe', '.bat', '.cmd'] as $ext) {
                    $files[] = self::join($p, "{$name}{$ext}");
                }
            }
            foreach ($files as $file) {
                if (is_file($file) && is_executable($file)) {
                    return $file;
                }
            }
        }
        return null;
    }
    
}
