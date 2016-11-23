<?php

namespace heiing\pdk\test;

use heiing\pdk\file\tree\RecursiveDirectory;

/**
 * Tester
 *
 * @author hzm
 */
class Tester {

    /**
     *
     * @var \heiing\pdk\file\tree\RecursiveDirectory
     */
    private $dir = null;

    private $testPath = '';
    private $calculateClassName = null;
    
    private $ignoredCallback = null;
    
    /**
     * 
     * @param string $testPath
     * @param callable $calculateClassNameCallback see setCalculateClassNameCallback
     */
    public function __construct($testPath, $calculateClassNameCallback = null) {
        $this->testPath = $testPath;
        $this->dir = new RecursiveDirectory($this->testPath);
        if (null === $calculateClassNameCallback) {
            $calculateClassNameCallback = function ($pathname) {
                return substr(basename($pathname), 0, -4);
            };
        }
        $this->setCalculateClassNameCallback($calculateClassNameCallback);
    }
    
    /**
     * 设置获取 classname 的回调函数。
     * 该回调函数接受两个参数：<br />
     * + $pathname - 测试文件的绝对路径<br />
     * + $tester   - \heiing\pdk\test\Tester 对象<br />
     * 并返回测试文件的类名
     * @param callable $callback
     * @throws TestException
     */
    public function setCalculateClassNameCallback($callback) {
        if (!is_callable($callback)) {
            throw new TestException("param callback is not callable!");
        }
        $this->calculateClassName = $callback;
    }
    
    public function getCalculateClassNameCallback() {
        return $this->calculateClassName;
    }
    
    /**
     * 设置要忽略测试的文件的回调函数。
     * 该回调函数接受两个参数：<br />
     * + $pathname - 测试文件的绝对路径<br />
     * + $tester   - \heiing\pdk\test\Tester 对象<br />
     * 并返回 true (忽略) 或 false (需要测试)
     * @param callable $callback
     * @throws TestException
     */
    public function setIgnoredCallback($callback) {
        $this->ignoredCallback = $callback;
    }
    
    public function getIgnoredCallback() {
        return $this->ignoredCallback;
    }
    
    /**
     * 返回测试文件所属的根目录
     * @return string
     */
    public function getTestPath() {
        return $this->testPath;
    }
    
    public function run() {
        $tester = $this;
        $this->dir->eachEntry(function ($name, \SplFileInfo $fileinfo) use ($tester) {
            $pathname = $fileinfo->getPathname();
            if ('Test.php' !== substr($name, -8)) {
                echo "Test Skip(Test file should be end with 'Test.php'): {$pathname}\n";
                return;
            }
            $ignored = $tester->getIgnoredCallback();
            if ((null !== $ignored) && (true === $ignored($pathname, $tester))) {
                echo "Test Skip(User Ignored): {$pathname}\n";
                return;
            }
            require $pathname;
            $calculateClassName = $tester->getCalculateClassNameCallback();
            $classname = $calculateClassName($pathname, $tester);
            if (!class_exists($classname)) {
                echo "Test Skip(Test class [{$classname}] not load): {$pathname}\n";
                return;
            }
            $methods = get_class_methods($classname);
            foreach ($methods as $method) {
                if ('test' !== substr($method, 0, 4)) {
                    continue;
                }
                call_user_func([new $classname, $method]);
            }
        });
        Test::report();
    }
    
}
