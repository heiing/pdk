<?php

namespace heiing\pdk\logs\writer;

/**
 * FileWriter: 将日志内容写入到文件
 *
 * @author hzm
 */
class FileWriter implements Writer {
    
    private $file = null;
    
    /**
     * 构建一个文件 Writer。
     * @param string|file_resource $file    例如 '/tmp/app.log', STDERR
     * @throws \heiing\pdk\logs\LogException
     */
    public function __construct($file) {
        if (is_resource($file)) {
            $this->file = $file;
        } else if (is_string($file)) {
            $this->file = fopen($file, 'ab');
            if (false === $this->file) {
                throw LogException::badFile("can not open '{$file}'");
            }
        }
        if (defined('STDIN') && (STDIN === $this->file)) {
            $this->file = STDOUT;
            throw LogException::badFile("Stdin is not writable");
        }
    }
    
    /**
     * 将文本写入文件，返回写入的字节数
     * @param string $buffer
     * @return int the count of bytes writed
     */
    public function write($buffer) {
        return fwrite($this->file, $buffer);
    }

}
